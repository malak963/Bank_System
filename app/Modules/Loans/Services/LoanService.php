<?php

namespace App\Modules\Loans\Services;

use App\Modules\Accounts\Models\Account;
use App\Modules\Installments\Enums\InstallmentStatus;
use App\Modules\Installments\Services\InstallmentService;
use App\Modules\LoanTypes\Enums\LoanTypeStatus;
use App\Modules\LoanTypes\Models\LoanType;
use App\Modules\Loans\Contracts\LoanRepositoryContract;
use App\Modules\Loans\Enums\LoanPaymentMethod;
use App\Modules\Loans\Enums\LoanStatus;
use App\Modules\Loans\Models\Loan;
use App\Modules\Loans\Models\LoanPayment;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    public function __construct(
        private LoanRepositoryContract $loans,
        private LoanReferenceGenerator $referenceGenerator,
        private InstallmentService $installments,
    ) {}

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->loans->paginate($filters, $perPage);
    }

    public function summary(array $filters = []): array
    {
        return $this->loans->summary($filters);
    }

    public function create(array $data): Loan
    {
        return DB::transaction(function () use ($data): Loan {
            $loanType = LoanType::query()
                ->whereKey($data['loan_type_id'])
                ->where('status', LoanTypeStatus::Active->value)
                ->first();

            if ($loanType === null) {
                throw ValidationException::withMessages(['loan_type_id' => 'The selected loan type is inactive.']);
            }

            $requestedAmount = round((float) $data['requested_amount'], 2);
            $termMonths = (int) $data['term_months'];
            $this->assertTypeLimits($loanType, $requestedAmount, $termMonths);

            $account = Account::query()
                ->whereKey($data['account_id'])
                ->where('customer_id', $data['customer_id'])
                ->where('status', 'open')
                ->first();

            if ($account === null) {
                throw ValidationException::withMessages(['account_id' => 'The repayment account must belong to the customer and be open.']);
            }

            return $this->loans->create([
                'loan_reference' => $this->referenceGenerator->generate(),
                'customer_id' => $data['customer_id'],
                'account_id' => $account->id,
                'loan_type_id' => $loanType->id,
                'requested_amount' => $requestedAmount,
                'annual_interest_rate' => $loanType->annual_interest_rate,
                'term_months' => $termMonths,
                'interest_method' => $loanType->interest_method,
                'repayment_frequency' => $loanType->repayment_frequency,
                'purpose' => $data['purpose'] ?? null,
                'status' => LoanStatus::Pending,
                'application_date' => today(),
            ]);
        });
    }

    public function approve(Loan $loan, array $data, int $approverId): Loan
    {
        if (! in_array($loan->status, [LoanStatus::Pending, LoanStatus::UnderReview], true)) {
            throw ValidationException::withMessages(['loan' => 'Only pending loan applications can be approved.']);
        }

        return DB::transaction(function () use ($loan, $data, $approverId): Loan {
            $approvedAmount = round((float) ($data['approved_amount'] ?? $loan->requested_amount), 2);
            $this->assertTypeLimits($loan->loanType, $approvedAmount, (int) $loan->term_months);
            $firstPaymentDate = CarbonImmutable::parse($data['first_payment_date'] ?? now()->addMonths($loan->repayment_frequency->monthsPerPeriod())->toDateString());
            $schedule = $this->installments->createSchedule($loan, $approvedAmount, $firstPaymentDate);

            $loan->fill([
                'approved_amount' => $approvedAmount,
                'annual_interest_rate' => $loan->annual_interest_rate,
                'approved_at' => now(),
                'approved_by' => $approverId,
                'first_payment_date' => $firstPaymentDate->toDateString(),
                'total_interest' => $schedule['total_interest'],
                'total_payable' => $schedule['total_payable'],
                'total_paid' => 0,
                'outstanding_principal' => $approvedAmount,
                'outstanding_interest' => $schedule['total_interest'],
                'next_payment_date' => $firstPaymentDate->toDateString(),
                'status' => LoanStatus::Approved,
                'rejection_reason' => null,
                'rejected_at' => null,
            ]);
            $loan->save();

            return $loan->refresh();
        });
    }

    public function reject(Loan $loan, string $reason): Loan
    {
        if (! in_array($loan->status, [LoanStatus::Pending, LoanStatus::UnderReview], true)) {
            throw ValidationException::withMessages(['loan' => 'Only pending loan applications can be rejected.']);
        }

        return DB::transaction(fn (): Loan => $this->loans->update($loan, [
            'status' => LoanStatus::Rejected,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]));
    }

    public function disburse(Loan $loan): Loan
    {
        if ($loan->status !== LoanStatus::Approved) {
            throw ValidationException::withMessages(['loan' => 'Only approved loans can be disbursed.']);
        }

        return DB::transaction(function () use ($loan): Loan {
            $account = Account::query()->whereKey($loan->account_id)->lockForUpdate()->first();

            if ($account === null || ! $account->isOpen()) {
                throw ValidationException::withMessages(['loan' => 'The linked account must be open before disbursement.']);
            }

            $account->balance = $this->money((float) $account->balance + (float) $loan->approved_amount);
            $account->save();

            return $this->loans->update($loan, [
                'status' => LoanStatus::Active,
                'disbursed_amount' => $loan->approved_amount,
                'disbursed_at' => now(),
            ]);
        });
    }

    public function recordPayment(Loan $loan, array $data, int $receiverId): Loan
    {
        if (! in_array($loan->status, [LoanStatus::Disbursed, LoanStatus::Active], true)) {
            throw ValidationException::withMessages(['loan' => 'Payments can only be recorded for an active loan.']);
        }

        return DB::transaction(function () use ($loan, $data, $receiverId): Loan {
            $amount = round((float) $data['amount'], 2);
            $outstanding = round((float) $loan->outstanding_principal + (float) $loan->outstanding_interest, 2);

            if ($amount <= 0 || $amount > $outstanding) {
                throw ValidationException::withMessages(['amount' => 'The payment must be positive and not exceed the outstanding balance.']);
            }

            $method = $data['payment_method'];
            if ($method === LoanPaymentMethod::AccountDebit->value) {
                $account = Account::query()->whereKey($loan->account_id)->lockForUpdate()->first();
                if ($account === null || ! $account->isOpen() || (float) $account->balance < $amount) {
                    throw ValidationException::withMessages(['amount' => 'The linked account does not have enough available balance.']);
                }
                $account->balance = $this->money((float) $account->balance - $amount);
                $account->save();
            }

            $payment = LoanPayment::create([
                'loan_id' => $loan->id,
                'payment_reference' => 'PMT-'.now()->format('YmdHis').'-'.random_int(100, 999),
                'amount' => $amount,
                'payment_method' => $method,
                'paid_at' => $data['paid_at'] ?? now(),
                'received_by' => $receiverId,
                'notes' => $data['notes'] ?? null,
            ]);
            $allocation = $this->installments->allocatePayment($payment, $amount);
            $payment->update($allocation);

            $loan->refresh();
            $outstandingPrincipal = (float) $loan->installments()->sum(DB::raw('principal_amount - principal_paid'));
            $outstandingInterest = (float) $loan->installments()->sum(DB::raw('interest_amount - interest_paid'));
            $totalPaid = round((float) $loan->total_paid + $amount, 2);
            $isPaidOff = round($outstandingPrincipal + $outstandingInterest, 2) <= 0;
            $nextInstallment = $loan->installments()
                ->whereIn('status', [InstallmentStatus::Pending->value, InstallmentStatus::PartiallyPaid->value])
                ->orderBy('installment_number')
                ->first();

            return $this->loans->update($loan, [
                'total_paid' => $totalPaid,
                'outstanding_principal' => max(0, $outstandingPrincipal),
                'outstanding_interest' => max(0, $outstandingInterest),
                'next_payment_date' => $nextInstallment?->due_date,
                'status' => $isPaidOff ? LoanStatus::PaidOff : LoanStatus::Active,
            ]);
        });
    }

    private function assertTypeLimits(LoanType $loanType, float $amount, int $termMonths): void
    {
        if ($amount < (float) $loanType->minimum_amount || $amount > (float) $loanType->maximum_amount) {
            throw ValidationException::withMessages(['requested_amount' => 'The amount is outside the selected loan type limits.']);
        }

        if ($termMonths < $loanType->minimum_term_months || $termMonths > $loanType->maximum_term_months) {
            throw ValidationException::withMessages(['term_months' => 'The term is outside the selected loan type limits.']);
        }
    }

    private function money(float $amount): float
    {
        return round($amount, 2);
    }
}

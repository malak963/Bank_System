<?php

namespace App\Modules\Installments\Services;

use App\Modules\Installments\Enums\InstallmentStatus;
use App\Modules\Installments\Contracts\InstallmentRepositoryContract;
use App\Modules\Installments\Models\Installment;
use App\Modules\Loans\Models\Loan;
use App\Modules\Loans\Models\LoanPayment;
use App\Modules\Loans\Models\LoanPaymentAllocation;
use App\Modules\Loans\Services\LoanCalculator;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InstallmentService
{
    public function __construct(
        private LoanCalculator $calculator,
        private InstallmentRepositoryContract $installments,
    ) {}

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->installments->paginate($filters, $perPage);
    }

    public function summary(): array
    {
        return $this->installments->summary();
    }

    public function createSchedule(Loan $loan, float $principal, CarbonInterface $firstPaymentDate): array
    {
        $result = $this->calculator->schedule(
            $principal,
            (float) $loan->annual_interest_rate,
            (int) $loan->term_months,
            $loan->interest_method,
            $loan->repayment_frequency,
            $firstPaymentDate,
        );

        $loan->installments()->delete();

        foreach ($result['installments'] as $installment) {
            $loan->installments()->create($installment);
        }

        return $result;
    }

    public function allocatePayment(LoanPayment $payment, float $amount): array
    {
        $remainingCents = $this->toCents($amount);
        $principalCents = 0;
        $interestCents = 0;

        $installments = $payment->loan
            ->installments()
            ->whereIn('status', [InstallmentStatus::Pending->value, InstallmentStatus::PartiallyPaid->value])
            ->orderBy('installment_number')
            ->lockForUpdate()
            ->get();

        foreach ($installments as $installment) {
            if ($remainingCents <= 0) {
                break;
            }

            $interestRemaining = max(0, $this->toCents($installment->interest_amount) - $this->toCents($installment->interest_paid));
            $principalRemaining = max(0, $this->toCents($installment->principal_amount) - $this->toCents($installment->principal_paid));
            $interestAllocation = min($remainingCents, $interestRemaining);
            $remainingCents -= $interestAllocation;
            $principalAllocation = min($remainingCents, $principalRemaining);
            $remainingCents -= $principalAllocation;
            $allocatedCents = $interestAllocation + $principalAllocation;

            if ($allocatedCents <= 0) {
                continue;
            }

            $newInterestPaid = $this->toCents($installment->interest_paid) + $interestAllocation;
            $newPrincipalPaid = $this->toCents($installment->principal_paid) + $principalAllocation;
            $newAmountPaid = $newInterestPaid + $newPrincipalPaid;
            $amountDueCents = $this->toCents($installment->amount_due);
            $isPaid = $newAmountPaid >= $amountDueCents;

            $installment->update([
                'interest_paid' => $this->fromCents($newInterestPaid),
                'principal_paid' => $this->fromCents($newPrincipalPaid),
                'amount_paid' => $this->fromCents($newAmountPaid),
                'status' => $isPaid ? InstallmentStatus::Paid : InstallmentStatus::PartiallyPaid,
                'paid_at' => $isPaid ? now() : null,
                'last_payment_at' => now(),
            ]);

            LoanPaymentAllocation::create([
                'loan_payment_id' => $payment->id,
                'installment_id' => $installment->id,
                'amount' => $this->fromCents($allocatedCents),
                'principal_amount' => $this->fromCents($principalAllocation),
                'interest_amount' => $this->fromCents($interestAllocation),
            ]);

            $principalCents += $principalAllocation;
            $interestCents += $interestAllocation;
        }

        if ($remainingCents > 0) {
            throw ValidationException::withMessages(['amount' => 'The payment exceeds the loan outstanding balance.']);
        }

        return [
            'principal_amount' => $this->fromCents($principalCents),
            'interest_amount' => $this->fromCents($interestCents),
        ];
    }

    private function toCents(mixed $amount): int
    {
        return (int) round((float) $amount * 100);
    }

    private function fromCents(int $amount): float
    {
        return round($amount / 100, 2);
    }
}

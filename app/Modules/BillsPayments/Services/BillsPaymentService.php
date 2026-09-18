<?php

namespace App\Modules\BillsPayments\Services;

use App\Modules\BillsPayments\Enums\BillStatus;
use App\Modules\BillsPayments\Models\Bill;
use App\Modules\Transactions\Services\TransactionService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BillsPaymentService
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    public function getAllBills(array $filters = []): Collection
    {
        $query = Bill::with(['customer', 'account', 'transaction']);

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['account_id'])) {
            $query->where('account_id', $filters['account_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['bill_type'])) {
            $query->where('bill_type', $filters['bill_type']);
        }

        if (isset($filters['overdue']) && $filters['overdue']) {
            $query->overdue();
        }

        return $query->orderBy('due_date', 'asc')->get();
    }

    public function getBill(int $id): Bill
    {
        return Bill::with(['customer', 'account', 'transaction'])->findOrFail($id);
    }

    public function createBill(array $data): Bill
    {
        return DB::transaction(function () use ($data) {
            return Bill::create([
                'bill_reference' => $this->generateReference(),
                'customer_id' => $data['customer_id'],
                'account_id' => $data['account_id'] ?? null,
                'bill_type' => $data['bill_type'],
                'provider_name' => $data['provider_name'],
                'provider_account_number' => $data['provider_account_number'] ?? null,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'SYP',
                'due_date' => $data['due_date'],
                'status' => BillStatus::PENDING,
                'description' => $data['description'] ?? null,
                'metadata' => $data['metadata'] ?? [],
            ]);
        });
    }

    public function updateBill(int $id, array $data): Bill
    {
        $bill = $this->getBill($id);
        $bill->update($data);
        return $bill;
    }

    public function payBill(int $id, int $accountId): Bill
    {
        return DB::transaction(function () use ($id, $accountId) {
            $bill = $this->getBill($id);
            
            if ($bill->status === BillStatus::COMPLETED) {
                throw new \Exception('Bill already paid');
            }

            // Create transaction
            $transaction = $this->transactionService->createTransaction([
                'account_id' => $accountId,
                'transaction_type' => 'bill_payment',
                'amount' => $bill->amount,
                'currency' => $bill->currency,
                'description' => "Bill payment: {$bill->provider_name} - {$bill->bill_reference}",
                'reference_number' => $bill->bill_reference,
            ]);

            // Update bill status
            $bill->update([
                'status' => BillStatus::COMPLETED,
                'paid_at' => now(),
                'transaction_id' => $transaction->id,
                'account_id' => $accountId,
            ]);

            return $bill->fresh();
        });
    }

    public function scheduleBill(int $id, string $scheduledDate): Bill
    {
        $bill = $this->getBill($id);
        $bill->update([
            'status' => BillStatus::SCHEDULED,
            'metadata' => array_merge($bill->metadata ?? [], [
                'scheduled_for' => $scheduledDate,
            ]),
        ]);
        return $bill;
    }

    public function cancelBill(int $id): Bill
    {
        $bill = $this->getBill($id);
        $bill->update(['status' => BillStatus::CANCELLED]);
        return $bill;
    }

    public function refundBill(int $id, string $reason): Bill
    {
        return DB::transaction(function () use ($id, $reason) {
            $bill = $this->getBill($id);
            
            if ($bill->status !== BillStatus::COMPLETED) {
                throw new \Exception('Only completed bills can be refunded');
            }

            // Create refund transaction
            if ($bill->transaction_id) {
                $this->transactionService->reverseTransaction($bill->transaction_id, $reason);
            }

            $bill->update([
                'status' => BillStatus::REFUNDED,
                'metadata' => array_merge($bill->metadata ?? [], [
                    'refund_reason' => $reason,
                    'refunded_at' => now()->toDateTimeString(),
                ]),
            ]);

            return $bill->fresh();
        });
    }

    public function deleteBill(int $id): bool
    {
        return Bill::findOrFail($id)->delete();
    }

    public function getOverdueBills(): Collection
    {
        return Bill::with(['customer', 'account'])
            ->overdue()
            ->orderBy('due_date', 'asc')
            ->get();
    }

    public function getUpcomingBills(int $days = 7): Collection
    {
        return Bill::with(['customer', 'account'])
            ->whereIn('status', [BillStatus::PENDING, BillStatus::SCHEDULED])
            ->whereBetween('due_date', [now(), now()->addDays($days)])
            ->orderBy('due_date', 'asc')
            ->get();
    }

    private function generateReference(): string
    {
        return 'BILL-' . strtoupper(uniqid());
    }
}

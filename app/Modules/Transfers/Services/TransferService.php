<?php

namespace App\Modules\Transfers\Services;

use App\Modules\Accounts\Models\Account;
use App\Modules\Customers\Models\Customer;
use App\Modules\Transfers\Enums\TransferStatus;
use App\Modules\Transfers\Enums\TransferType;
use App\Modules\Transfers\Models\Transfer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferService
{
    public function createTransfer(array $data): Transfer
    {
        return DB::transaction(function () use ($data) {
            $transferType = TransferType::from($data['transfer_type']);
            
            $transfer = Transfer::create([
                'transfer_reference' => $this->generateTransferReference(),
                'from_account_id' => $data['from_account_id'],
                'to_account_id' => $data['to_account_id'] ?? null,
                'customer_id' => $data['customer_id'],
                'transfer_type' => $transferType,
                'status' => TransferStatus::Pending,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'USD',
                'exchange_rate' => $data['exchange_rate'] ?? null,
                'converted_amount' => $data['converted_amount'] ?? null,
                'fees' => $data['fees'] ?? $this->calculateFees($transferType, $data['amount']),
                'total_deducted' => 0,
                'recipient_name' => $data['recipient_name'] ?? null,
                'recipient_account' => $data['recipient_account'] ?? null,
                'recipient_bank' => $data['recipient_bank'] ?? null,
                'recipient_bank_address' => $data['recipient_bank_address'] ?? null,
                'swift_code' => $data['swift_code'] ?? null,
                'iban' => $data['iban'] ?? null,
                'routing_number' => $data['routing_number'] ?? null,
                'reference' => $data['reference'] ?? null,
                'description' => $data['description'] ?? null,
                'scheduled_for' => $data['scheduled_for'] ?? null,
                'metadata' => $data['metadata'] ?? null,
            ]);

            // Process immediately if not scheduled
            if (!$transfer->isScheduled()) {
                $this->processTransfer($transfer);
            }

            Log::info("Transfer created successfully", [
                'transfer_id' => $transfer->id,
                'transfer_reference' => $transfer->transfer_reference,
                'transfer_type' => $transferType->value,
                'amount' => $transfer->amount,
            ]);

            return $transfer->fresh();
        });
    }

    public function internalTransfer(Account $fromAccount, Account $toAccount, float $amount, string $description = ''): Transfer
    {
        return $this->createTransfer([
            'from_account_id' => $fromAccount->id,
            'to_account_id' => $toAccount->id,
            'customer_id' => $fromAccount->customer_id,
            'transfer_type' => TransferType::Internal,
            'amount' => $amount,
            'description' => $description ?: "Internal transfer to account {$toAccount->account_number}",
        ]);
    }

    public function externalTransfer(Account $fromAccount, array $beneficiary, float $amount, string $description = ''): Transfer
    {
        return $this->createTransfer([
            'from_account_id' => $fromAccount->id,
            'customer_id' => $fromAccount->customer_id,
            'transfer_type' => TransferType::External,
            'amount' => $amount,
            'recipient_name' => $beneficiary['name'],
            'recipient_account' => $beneficiary['account_number'],
            'recipient_bank' => $beneficiary['bank_name'],
            'recipient_bank_address' => $beneficiary['bank_address'] ?? null,
            'routing_number' => $beneficiary['routing_number'] ?? null,
            'reference' => $beneficiary['reference'] ?? null,
            'description' => $description ?: "External transfer to {$beneficiary['name']}",
        ]);
    }

    public function internationalTransfer(Account $fromAccount, array $beneficiary, float $amount, string $currency, float $exchangeRate, string $description = ''): Transfer
    {
        $convertedAmount = $amount * $exchangeRate;

        return $this->createTransfer([
            'from_account_id' => $fromAccount->id,
            'customer_id' => $fromAccount->customer_id,
            'transfer_type' => TransferType::International,
            'amount' => $amount,
            'currency' => $currency,
            'exchange_rate' => $exchangeRate,
            'converted_amount' => $convertedAmount,
            'recipient_name' => $beneficiary['name'],
            'recipient_account' => $beneficiary['account_number'],
            'recipient_bank' => $beneficiary['bank_name'],
            'recipient_bank_address' => $beneficiary['bank_address'] ?? null,
            'swift_code' => $beneficiary['swift_code'],
            'iban' => $beneficiary['iban'],
            'reference' => $beneficiary['reference'] ?? null,
            'description' => $description ?: "International transfer to {$beneficiary['name']}",
        ]);
    }

    public function processTransfer(Transfer $transfer): bool
    {
        try {
            $transfer->update([
                'status' => TransferStatus::Processing,
                'processed_at' => now(),
            ]);

            $fromAccount = $transfer->fromAccount;
            
            // Validate sufficient balance
            $totalDeduction = $transfer->amount + $transfer->fees;
            if ($fromAccount->balance < $totalDeduction) {
                throw new \Exception('Insufficient funds for transfer');
            }

            // Debit from source account
            $fromAccount->update(['balance' => $fromAccount->balance - $totalDeduction]);
            $transfer->update(['total_deducted' => $totalDeduction]);

            // Create debit transaction
            $debitTransaction = $this->createTransaction($transfer, 'debit');
            
            // Process based on transfer type
            if ($transfer->transfer_type === TransferType::Internal && $transfer->toAccount) {
                // Credit destination account
                $toAccount = $transfer->toAccount;
                $toAccount->update(['balance' => $toAccount->balance + $transfer->amount]);
                
                // Create credit transaction
                $this->createTransaction($transfer, 'credit', $toAccount->id);
            }

            $transfer->update([
                'status' => TransferStatus::Completed,
                'completed_at' => now(),
            ]);

            Log::info("Transfer processed successfully", [
                'transfer_id' => $transfer->id,
                'transfer_reference' => $transfer->transfer_reference,
                'amount' => $transfer->amount,
            ]);

            return true;

        } catch (\Exception $e) {
            $transfer->update([
                'status' => TransferStatus::Failed,
                'failed_at' => now(),
                'failure_reason' => $e->getMessage(),
            ]);

            Log::error("Transfer processing failed", [
                'transfer_id' => $transfer->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function cancelTransfer(Transfer $transfer, string $reason): Transfer
    {
        if (!$transfer->status->canBeCancelled()) {
            throw new \Exception('This transfer cannot be cancelled');
        }

        $transfer->update([
            'status' => TransferStatus::Cancelled,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        Log::info("Transfer cancelled", [
            'transfer_id' => $transfer->id,
            'reason' => $reason,
        ]);

        return $transfer->fresh();
    }

    public function retryTransfer(Transfer $transfer): bool
    {
        if (!$transfer->status->canBeRetried()) {
            throw new \Exception('This transfer cannot be retried');
        }

        // Reset transfer status
        $transfer->update([
            'status' => TransferStatus::Pending,
            'failed_at' => null,
            'failure_reason' => null,
        ]);

        return $this->processTransfer($transfer);
    }

    public function processScheduledTransfers(): int
    {
        $scheduledTransfers = Transfer::scheduled()
            ->where('scheduled_for', '<=', now())
            ->get();

        $processedCount = 0;

        foreach ($scheduledTransfers as $transfer) {
            if ($this->processTransfer($transfer)) {
                $processedCount++;
            }
        }

        return $processedCount;
    }

    public function getTransfers(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = Transfer::query();

        if (isset($filters['customer_id'])) {
            $query->byCustomer($filters['customer_id']);
        }

        if (isset($filters['account_id'])) {
            $query->byAccount($filters['account_id']);
        }

        if (isset($filters['transfer_type'])) {
            $query->where('transfer_type', $filters['transfer_type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['scheduled_only']) && $filters['scheduled_only']) {
            $query->scheduled();
        }

        return $query->with(['fromAccount', 'toAccount', 'customer'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getTransferStatistics(): array
    {
        $total = Transfer::count();
        $pending = Transfer::pending()->count();
        $processing = Transfer::where('status', TransferStatus::Processing)->count();
        $completed = Transfer::completed()->count();
        $failed = Transfer::where('status', TransferStatus::Failed)->count();
        $cancelled = Transfer::where('status', TransferStatus::Cancelled)->count();

        $totalAmount = Transfer::completed()->sum('amount');
        $totalFees = Transfer::completed()->sum('fees');

        $byType = Transfer::selectRaw('transfer_type, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('transfer_type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->transfer_type => [
                    'count' => $item->count,
                    'total_amount' => $item->total_amount,
                ]];
            })
            ->toArray();

        return [
            'total' => $total,
            'pending' => $pending,
            'processing' => $processing,
            'completed' => $completed,
            'failed' => $failed,
            'cancelled' => $cancelled,
            'total_amount' => $totalAmount,
            'total_fees' => $totalFees,
            'by_type' => $byType,
        ];
    }

    private function calculateFees(TransferType $type, float $amount): float
    {
        return match ($type) {
            TransferType::Internal => min($amount * 0.01, 5), // 1% or $5 max
            TransferType::External => min($amount * 0.025, 25), // 2.5% or $25 max
            TransferType::International => min($amount * 0.05, 50), // 5% or $50 max
            TransferType::StandingOrder => 0,
            TransferType::DirectDebit => 0,
        };
    }

    private function createTransaction(Transfer $transfer, string $type, ?int $toAccountId = null): \App\Modules\Transactions\Models\Transaction
    {
        $transactionService = app(\App\Modules\Transactions\Services\TransactionService::class);
        
        $transactionData = [
            'account_id' => $type === 'debit' ? $transfer->from_account_id : $toAccountId,
            'transaction_type' => $type === 'debit' ? \App\Modules\Transactions\Enums\TransactionType::Transfer : \App\Modules\Transactions\Enums\TransactionType::Deposit,
            'amount' => $transfer->amount,
            'description' => $transfer->description,
            'reference_number' => $transfer->transfer_reference,
            'category' => 'transfer',
            'metadata' => [
                'transfer_id' => $transfer->id,
                'transfer_reference' => $transfer->transfer_reference,
                'transfer_type' => $transfer->transfer_type->value,
            ],
        ];

        return $transactionService->createTransaction($transactionData);
    }

    private function generateTransferReference(): string
    {
        return 'TRF' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}

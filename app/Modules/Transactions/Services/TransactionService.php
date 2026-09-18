<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Accounts\Models\Account;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Enums\TransactionType;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    public function createTransaction(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $account = Account::findOrFail($data['account_id']);
            
            $transactionType = is_string($data['transaction_type']) ? TransactionType::from($data['transaction_type']) : $data['transaction_type'];
            $this->validateTransaction($account, $transactionType, $data['amount']);

            $transaction = Transaction::create([
                'transaction_reference' => $this->generateTransactionReference(),
                'branch_id' => $account->branch_id,
                'customer_id' => $account->customer_id,
                'account_id' => $account->id,
                'transaction_type' => $transactionType,
                'amount' => $data['amount'],
                'balance_before' => $account->balance,
                'balance_after' => $this->calculateNewBalance($account, $transactionType, $data['amount']),
                'currency' => $account->currency ?? 'USD',
                'status' => TransactionStatus::Processing,
                'description' => $data['description'] ?? $this->getDefaultDescription($transactionType),
                'reference_number' => $data['reference_number'] ?? null,
                'related_transaction_id' => $data['related_transaction_id'] ?? null,
                'fees' => $data['fees'] ?? 0,
                'tax' => $data['tax'] ?? 0,
                'category' => $data['category'] ?? null,
                'sub_category' => $data['sub_category'] ?? null,
                'tags' => $data['tags'] ?? null,
                'metadata' => $data['metadata'] ?? null,
                'created_by' => auth()->id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'device_id' => $data['device_id'] ?? null,
            ]);

            $this->processTransaction($transaction, $account);

            return $transaction->fresh();
        });
    }

    public function deposit(Account $account, float $amount, array $metadata = []): Transaction
    {
        return $this->createTransaction([
            'account_id' => $account->id,
            'transaction_type' => TransactionType::Deposit,
            'amount' => $amount,
            'description' => 'Cash deposit',
            'metadata' => $metadata,
        ]);
    }

    public function withdrawal(Account $account, float $amount, array $metadata = []): Transaction
    {
        return $this->createTransaction([
            'account_id' => $account->id,
            'transaction_type' => TransactionType::Withdrawal,
            'amount' => $amount,
            'description' => 'Cash withdrawal',
            'metadata' => $metadata,
        ]);
    }

    public function transfer(Account $fromAccount, Account $toAccount, float $amount, array $metadata = []): array
    {
        return DB::transaction(function () use ($fromAccount, $toAccount, $amount, $metadata) {
            $debitTransaction = $this->createTransaction([
                'account_id' => $fromAccount->id,
                'transaction_type' => TransactionType::Transfer,
                'amount' => $amount,
                'description' => "Transfer to account {$toAccount->account_number}",
                'metadata' => array_merge($metadata, [
                    'recipient_account' => $toAccount->account_number,
                    'recipient_name' => $toAccount->customer->full_name ?? 'Unknown',
                ]),
            ]);

            $creditTransaction = $this->createTransaction([
                'account_id' => $toAccount->id,
                'transaction_type' => TransactionType::Deposit,
                'amount' => $amount,
                'description' => "Transfer from account {$fromAccount->account_number}",
                'related_transaction_id' => $debitTransaction->id,
                'metadata' => array_merge($metadata, [
                    'sender_account' => $fromAccount->account_number,
                    'sender_name' => $fromAccount->customer->full_name ?? 'Unknown',
                ]),
            ]);

            return [
                'debit' => $debitTransaction,
                'credit' => $creditTransaction,
            ];
        });
    }

    public function reverseTransaction(Transaction $transaction, string $reason, ?int $reversedBy = null): Transaction
    {
        if (!$transaction->canBeReversed()) {
            throw new \Exception('This transaction cannot be reversed');
        }

        return DB::transaction(function () use ($transaction, $reason, $reversedBy) {
            $account = $transaction->account;
            
            $reversalTransaction = Transaction::create([
                'transaction_reference' => $this->generateTransactionReference(),
                'branch_id' => $transaction->branch_id,
                'customer_id' => $transaction->customer_id,
                'account_id' => $account->id,
                'transaction_type' => TransactionType::Reversal,
                'amount' => $transaction->amount,
                'balance_before' => $account->balance,
                'balance_after' => $this->calculateReversalBalance($account, $transaction),
                'currency' => $transaction->currency,
                'status' => TransactionStatus::Completed,
                'description' => "Reversal of transaction {$transaction->transaction_reference}",
                'reference_number' => $transaction->reference_number,
                'related_transaction_id' => $transaction->id,
                'fees' => 0,
                'tax' => 0,
                'category' => 'reversal',
                'metadata' => [
                    'original_transaction' => $transaction->transaction_reference,
                    'original_amount' => $transaction->amount,
                    'original_type' => $transaction->transaction_type->value,
                ],
                'processed_at' => now(),
                'reversed_by' => $reversedBy ?? auth()->id(),
                'reversal_reason' => $reason,
                'created_by' => auth()->id(),
            ]);

            $this->updateAccountBalance($account, $reversalTransaction);

            $transaction->update([
                'status' => TransactionStatus::Reversed,
                'reversed_at' => now(),
                'reversed_by' => $reversedBy ?? auth()->id(),
                'reversal_reason' => $reason,
            ]);

            return $reversalTransaction;
        });
    }

    public function getTransactionHistory(Account $account, array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = $account->transactions();

        if (isset($filters['type'])) {
            $query->where('transaction_type', $filters['type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['from_date'])) {
            $query->where('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->where('created_at', '<=', $filters['to_date']);
        }

        if (isset($filters['min_amount'])) {
            $query->where('amount', '>=', $filters['min_amount']);
        }

        if (isset($filters['max_amount'])) {
            $query->where('amount', '<=', $filters['max_amount']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    private function validateTransaction(Account $account, mixed $type, float $amount): void
    {
        if (!$account->isOpen()) {
            throw new \Exception('Account is not active');
        }

        $transactionType = is_string($type) ? TransactionType::from($type) : $type;
        
        if ($transactionType->isDebit() && $account->balance < $amount) {
            throw new \Exception('Insufficient funds');
        }

        if ($amount <= 0) {
            throw new \Exception('Amount must be greater than zero');
        }
    }

    private function calculateNewBalance(Account $account, mixed $type, float $amount): float
    {
        $transactionType = is_string($type) ? TransactionType::from($type) : $type;
        
        if ($transactionType->isCredit()) {
            return $account->balance + $amount;
        }

        return $account->balance - $amount;
    }

    private function calculateReversalBalance(Account $account, Transaction $originalTransaction): float
    {
        if ($originalTransaction->isCredit()) {
            return $account->balance - $originalTransaction->amount;
        }

        return $account->balance + $originalTransaction->amount;
    }

    private function processTransaction(Transaction $transaction, Account $account): void
    {
        try {
            $this->updateAccountBalance($account, $transaction);

            $transaction->update([
                'status' => TransactionStatus::Completed,
                'processed_at' => now(),
            ]);

            Log::info("Transaction processed successfully", [
                'transaction_reference' => $transaction->transaction_reference,
                'account_id' => $account->id,
                'amount' => $transaction->amount,
            ]);

        } catch (\Exception $e) {
            $transaction->update([
                'status' => TransactionStatus::Failed,
                'failed_at' => now(),
                'failed_reason' => $e->getMessage(),
            ]);

            Log::error("Transaction processing failed", [
                'transaction_reference' => $transaction->transaction_reference,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function updateAccountBalance(Account $account, Transaction $transaction): void
    {
        $account->update(['balance' => $transaction->balance_after]);
    }

    private function generateTransactionReference(): string
    {
        return 'TXN' . date('Ymd') . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function getDefaultDescription(TransactionType $type): string
    {
        return match ($type) {
            TransactionType::Deposit => 'Deposit',
            TransactionType::Withdrawal => 'Withdrawal',
            TransactionType::Transfer => 'Transfer',
            TransactionType::Fee => 'Service fee',
            TransactionType::Interest => 'Interest payment',
            TransactionType::Penalty => 'Penalty charge',
            default => 'Transaction',
        };
    }
}

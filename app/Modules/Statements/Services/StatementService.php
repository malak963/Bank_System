<?php

namespace App\Modules\Statements\Services;

use App\Modules\Statements\Models\Statement;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StatementService
{
    public function getAllStatements(array $filters = []): Collection
    {
        $query = Statement::with(['account', 'customer']);

        if (isset($filters['account_id'])) {
            $query->where('account_id', $filters['account_id']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->orderBy('period_end', 'desc')->get();
    }

    public function getStatement(int $id): Statement
    {
        return Statement::with(['account', 'customer', 'transactions'])->findOrFail($id);
    }

    public function createStatement(array $data): Statement
    {
        return DB::transaction(function () use ($data) {
            $statement = Statement::create([
                'statement_reference' => $this->generateReference(),
                'account_id' => $data['account_id'],
                'customer_id' => $data['customer_id'],
                'period_start' => $data['period_start'],
                'period_end' => $data['period_end'],
                'opening_balance' => $data['opening_balance'] ?? 0,
                'status' => 'pending',
                'currency' => $data['currency'] ?? 'SYP',
            ]);

            // Calculate totals from transactions
            $transactions = Transaction::where('account_id', $data['account_id'])
                ->whereBetween('created_at', [$data['period_start'], $data['period_end']])
                ->get();

            $totalDebits = $transactions->where('transaction_type', 'withdrawal')->sum('amount') +
                          $transactions->where('transaction_type', 'bill_payment')->sum('amount');
            $totalCredits = $transactions->where('transaction_type', 'deposit')->sum('amount') +
                           $transactions->where('transaction_type', 'transfer')->where('amount', '>', 0)->sum('amount');

            $statement->update([
                'total_debits' => $totalDebits,
                'total_credits' => $totalCredits,
                'closing_balance' => $statement->opening_balance + $totalCredits - $totalDebits,
                'transaction_count' => $transactions->count(),
                'status' => 'completed',
                'generated_at' => now(),
            ]);

            return $statement->fresh();
        });
    }

    public function generateStatementFile(int $id): Statement
    {
        return DB::transaction(function () use ($id) {
            $statement = $this->getStatement($id);
            
            $statement->update(['status' => 'generating']);

            // Generate CSV file
            $filename = "statement_{$statement->id}_{$statement->statement_reference}.csv";
            $path = "statements/{$filename}";
            
            $content = $this->generateCSVContent($statement);
            
            Storage::disk('local')->put($path, $content);

            $statement->update([
                'status' => 'completed',
                'file_path' => $path,
                'file_size' => strlen($content),
            ]);

            return $statement->fresh();
        });
    }

    public function updateStatement(int $id, array $data): Statement
    {
        $statement = $this->getStatement($id);
        $statement->update($data);
        return $statement;
    }

    public function deleteStatement(int $id): bool
    {
        return Statement::findOrFail($id)->delete();
    }

    public function getCustomerStatements(int $customerId, int $limit = 12): Collection
    {
        return Statement::where('customer_id', $customerId)
            ->orderBy('period_end', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getAccountStatements(int $accountId, int $limit = 12): Collection
    {
        return Statement::where('account_id', $accountId)
            ->orderBy('period_end', 'desc')
            ->limit($limit)
            ->get();
    }

    private function generateCSVContent(Statement $statement): string
    {
        $lines = [];
        
        // Header
        $lines[] = 'Statement Reference,' . $statement->statement_reference;
        $lines[] = 'Account Number,' . $statement->account->account_number;
        $lines[] = 'Customer,' . $statement->customer->first_name . ' ' . $statement->customer->last_name;
        $lines[] = 'Period,' . $statement->period_start . ' to ' . $statement->period_end;
        $lines[] = 'Opening Balance,' . $statement->opening_balance;
        $lines[] = 'Closing Balance,' . $statement->closing_balance;
        $lines[] = 'Total Debits,' . $statement->total_debits;
        $lines->push('Total Credits,' . $statement->total_credits);
        $lines[] = 'Transaction Count,' . $statement->transaction_count;
        $lines[] = '';
        
        // Transactions
        $lines[] = 'Date,Reference,Type,Description,Amount,Balance';
        
        $transactions = Transaction::where('account_id', $statement->account_id)
            ->whereBetween('created_at', [$statement->period_start, $statement->period_end])
            ->orderBy('created_at')
            ->get();

        $balance = $statement->opening_balance;
        foreach ($transactions as $transaction) {
            if (in_array($transaction->transaction_type, ['deposit', 'transfer'])) {
                $balance += $transaction->amount;
            } else {
                $balance -= $transaction->amount;
            }
            
            $lines[] = implode(',', [
                $transaction->created_at->format('Y-m-d H:i:s'),
                $transaction->transaction_reference,
                $transaction->transaction_type,
                $transaction->description,
                $transaction->amount,
                $balance,
            ]);
        }

        return implode("\n", $lines);
    }

    private function generateReference(): string
    {
        return 'STMT-' . strtoupper(uniqid());
    }
}

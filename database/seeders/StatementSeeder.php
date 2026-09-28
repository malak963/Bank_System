<?php

namespace Database\Seeders;

use App\Modules\Accounts\Models\Account;
use App\Modules\Statements\Models\Statement;
use Illuminate\Database\Seeder;

class StatementSeeder extends Seeder
{
    public function run(): void
    {
        $ahmadAcc = Account::where('account_number', '1000100001')->first();
        $laylaAcc = Account::where('account_number', '1000200001')->first();

        $statements = [
            [
                'statement_reference' => 'STM-2026-08-0001',
                'account_id' => $ahmadAcc?->id,
                'customer_id' => $ahmadAcc?->customer_id,
                'period_start' => now()->subMonth()->startOfMonth()->format('Y-m-d'),
                'period_end' => now()->subMonth()->endOfMonth()->format('Y-m-d'),
                'opening_balance' => 9500000.00,
                'closing_balance' => 14500000.00,
                'total_debits' => 625000.00,
                'total_credits' => 5625000.00,
                'transaction_count' => 8,
                'currency' => 'SYP',
                'status' => 'completed',
                'file_path' => 'statements/2026/08/stm_1000100001_aug2026.pdf',
                'file_size' => 184320,
                'generated_at' => now()->subMonth()->endOfMonth(),
                'notes' => 'كشف الحساب الدوري لشهر آب 2026 - Monthly e-Statement',
            ],
            [
                'statement_reference' => 'STM-2026-08-0002',
                'account_id' => $laylaAcc?->id,
                'customer_id' => $laylaAcc?->customer_id,
                'period_start' => now()->subMonth()->startOfMonth()->format('Y-m-d'),
                'period_end' => now()->subMonth()->endOfMonth()->format('Y-m-d'),
                'opening_balance' => 0.00,
                'closing_balance' => 8200000.00,
                'total_debits' => 0.00,
                'total_credits' => 8200000.00,
                'transaction_count' => 2,
                'currency' => 'SYP',
                'status' => 'completed',
                'file_path' => 'statements/2026/08/stm_1000200001_aug2026.pdf',
                'file_size' => 145000,
                'generated_at' => now()->subMonth()->endOfMonth(),
                'notes' => 'كشف الحساب الدوري لشهر آب 2026 - Monthly e-Statement',
            ],
        ];

        foreach ($statements as $st) {
            if (!$st['account_id']) {
                continue;
            }

            Statement::updateOrCreate(
                ['statement_reference' => $st['statement_reference']],
                $st
            );
        }
    }
}

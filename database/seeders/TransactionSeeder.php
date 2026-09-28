<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\Accounts\Models\Account;
use App\Modules\Branches\Models\Branch;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Enums\TransactionType;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = Account::with('customer', 'customer.branch')->get()->keyBy('account_number');
        $teller = User::where('email', 'teller@bank.com')->first();
        $tellerId = $teller?->id;
        $branch = Branch::where('code', 'BR-DAM-01')->first() ?? Branch::first();
        $branchId = $branch?->id;

        $txns = [
            // Account 1000100001 (Ahmad Al-Sayed)
            [
                'reference' => 'TXN-2026-000001',
                'account_number' => '1000100001',
                'type' => TransactionType::Deposit,
                'amount' => 5000000.00,
                'before' => 9500000.00,
                'after' => 14500000.00,
                'currency' => 'SYP',
                'desc' => 'إيداع نقدي عبر الصندوق - Main Branch Cash Deposit',
                'category' => 'Cash Deposit',
                'date' => now()->subDays(2),
            ],
            [
                'reference' => 'TXN-2026-000002',
                'account_number' => '1000100001',
                'type' => TransactionType::Withdrawal,
                'amount' => 500000.00,
                'before' => 10000000.00,
                'after' => 9500000.00,
                'currency' => 'SYP',
                'desc' => 'سحب نقدي من الصراف الآلي - ATM Cash Withdrawal',
                'category' => 'ATM',
                'date' => now()->subDays(5),
            ],
            [
                'reference' => 'TXN-2026-000003',
                'account_number' => '1000100001',
                'type' => TransactionType::BillPayment,
                'amount' => 125000.00,
                'before' => 10125000.00,
                'after' => 10000000.00,
                'currency' => 'SYP',
                'desc' => 'تسديد فاتورة الكهرباء - Electricity Utility Bill Payment',
                'category' => 'Utilities',
                'date' => now()->subDays(10),
            ],
            [
                'reference' => 'TXN-2026-000004',
                'account_number' => '1000100001',
                'type' => TransactionType::Deposit,
                'amount' => 10125000.00,
                'before' => 0.00,
                'after' => 10125000.00,
                'currency' => 'SYP',
                'desc' => 'تحويل راتب شهري - Monthly Salary Direct Deposit',
                'category' => 'Salary',
                'date' => now()->subDays(25),
            ],

            // Account 1000100003 (Ahmad USD Account)
            [
                'reference' => 'TXN-2026-000005',
                'account_number' => '1000100003',
                'type' => TransactionType::Deposit,
                'amount' => 12450.00,
                'before' => 0.00,
                'after' => 12450.00,
                'currency' => 'USD',
                'desc' => 'حوالة واردة من الخارج - Incoming International Wire Transfer',
                'category' => 'Remittance',
                'date' => now()->subMonths(2),
            ],

            // Account 1000200001 (Layla Mansour)
            [
                'reference' => 'TXN-2026-000006',
                'account_number' => '1000200001',
                'type' => TransactionType::Deposit,
                'amount' => 8200000.00,
                'before' => 0.00,
                'after' => 8200000.00,
                'currency' => 'SYP',
                'desc' => 'إيداع شيك مصرفي - Bank Cheque Deposit',
                'category' => 'Cheque',
                'date' => now()->subDays(12),
            ],

            // Account 1000300002 (Omar Khaled Business)
            [
                'reference' => 'TXN-2026-000007',
                'account_number' => '1000300002',
                'type' => TransactionType::Deposit,
                'amount' => 50000000.00,
                'before' => 70000000.00,
                'after' => 120000000.00,
                'currency' => 'SYP',
                'desc' => 'مقبوضات تجارية عبر التحويل - Commercial Sales Settlement',
                'category' => 'Business',
                'date' => now()->subDays(3),
            ],
            [
                'reference' => 'TXN-2026-000008',
                'account_number' => '1000300002',
                'type' => TransactionType::Withdrawal,
                'amount' => 15000000.00,
                'before' => 85000000.00,
                'after' => 70000000.00,
                'currency' => 'SYP',
                'desc' => 'صرف رواتب موظفي الشركة - Payroll Corporate Disbursement',
                'category' => 'Payroll',
                'date' => now()->subDays(8),
            ],

            // Account 1000400001 (Nour Al-Hassan)
            [
                'reference' => 'TXN-2026-000009',
                'account_number' => '1000400001',
                'type' => TransactionType::BillPayment,
                'amount' => 85000.00,
                'before' => 5685000.00,
                'after' => 5600000.00,
                'currency' => 'SYP',
                'desc' => 'تسديد اشتراك الإنترنت - Internet Telecom Subscription Payment',
                'category' => 'Utilities',
                'date' => now()->subDays(4),
            ],

            // Account 1000500001 (Tariq Al-Halabi)
            [
                'reference' => 'TXN-2026-000010',
                'account_number' => '1000500001',
                'type' => TransactionType::Transfer,
                'amount' => 2000000.00,
                'before' => 11800000.00,
                'after' => 9800000.00,
                'currency' => 'SYP',
                'desc' => 'تحويل مالي داخلي - Internal Account-to-Account Transfer',
                'category' => 'Transfer',
                'date' => now()->subDays(6),
            ],
        ];

        foreach ($txns as $t) {
            $account = $accounts->get($t['account_number']);
            if (!$account) {
                continue;
            }

            Transaction::updateOrCreate(
                ['transaction_reference' => $t['reference']],
                [
                    'branch_id' => $account->customer?->branch_id ?? $branchId,
                    'customer_id' => $account->customer_id,
                    'account_id' => $account->id,
                    'transaction_type' => $t['type'],
                    'amount' => $t['amount'],
                    'balance_before' => $t['before'],
                    'balance_after' => $t['after'],
                    'currency' => $t['currency'],
                    'status' => TransactionStatus::Completed,
                    'description' => $t['desc'],
                    'reference_number' => 'REF-' . strtoupper(substr(md5($t['reference']), 0, 10)),
                    'category' => $t['category'],
                    'created_by' => $tellerId,
                    'processed_at' => $t['date'],
                    'created_at' => $t['date'],
                    'updated_at' => $t['date'],
                ]
            );
        }
    }
}

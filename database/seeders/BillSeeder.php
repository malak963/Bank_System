<?php

namespace Database\Seeders;

use App\Modules\Accounts\Models\Account;
use App\Modules\BillsPayments\Models\Bill;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Database\Seeder;

class BillSeeder extends Seeder
{
    public function run(): void
    {
        $ahmadAcc = Account::where('account_number', '1000100001')->first();
        $laylaAcc = Account::where('account_number', '1000200001')->first();
        $txn = Transaction::where('transaction_reference', 'TXN-2026-000003')->first();

        $bills = [
            [
                'bill_reference' => 'BIL-2026-0001',
                'customer_id' => $ahmadAcc?->customer_id,
                'account_id' => $ahmadAcc?->id,
                'bill_type' => 'electricity',
                'provider_name' => 'General Establishment of Electricity (Damascus)',
                'provider_account_number' => 'ELEC-DAM-9921',
                'amount' => 125000.00,
                'currency' => 'SYP',
                'due_date' => now()->subDays(10)->format('Y-m-d'),
                'status' => 'completed',
                'description' => 'فاتورة الكهرباء عن الدورة السابقة - Damascus Residential Electricity',
                'paid_at' => now()->subDays(10),
                'transaction_id' => $txn?->id,
            ],
            [
                'bill_reference' => 'BIL-2026-0002',
                'customer_id' => $ahmadAcc?->customer_id,
                'account_id' => $ahmadAcc?->id,
                'bill_type' => 'internet',
                'provider_name' => 'Syrian Telecom (Tarassul ADSL)',
                'provider_account_number' => 'STC-11002233',
                'amount' => 65000.00,
                'currency' => 'SYP',
                'due_date' => now()->addDays(5)->format('Y-m-d'),
                'status' => 'pending',
                'description' => 'اشتراك إنترنت فائق السرعة 8 ميغا - High Speed ADSL Subscription',
            ],
            [
                'bill_reference' => 'BIL-2026-0003',
                'customer_id' => $laylaAcc?->customer_id,
                'account_id' => $laylaAcc?->id,
                'bill_type' => 'phone',
                'provider_name' => 'Syriatel Mobile Telecom',
                'provider_account_number' => '0944111222',
                'amount' => 48000.00,
                'currency' => 'SYP',
                'due_date' => now()->addDays(8)->format('Y-m-d'),
                'status' => 'pending',
                'description' => 'فاتورة خط لاحق الدفع - Postpaid Mobile Line Plan',
            ],
            [
                'bill_reference' => 'BIL-2026-0004',
                'customer_id' => $ahmadAcc?->customer_id,
                'account_id' => $ahmadAcc?->id,
                'bill_type' => 'water',
                'provider_name' => 'Damascus Water Authority (Ain Al-Fijeh)',
                'provider_account_number' => 'WAT-DAM-4482',
                'amount' => 32000.00,
                'currency' => 'SYP',
                'due_date' => now()->subDays(20)->format('Y-m-d'),
                'status' => 'completed',
                'description' => 'فاتورة مياه الشرب - Residential Clean Water Utility Bill',
                'paid_at' => now()->subDays(20),
            ],
        ];

        foreach ($bills as $bill) {
            Bill::updateOrCreate(
                ['bill_reference' => $bill['bill_reference']],
                $bill
            );
        }
    }
}

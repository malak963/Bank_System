<?php

namespace Database\Seeders;

use App\Modules\Accounts\Enums\AccountStatus;
use App\Modules\Accounts\Models\Account;
use App\Modules\AccountTypes\Models\AccountType;
use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $currentType = AccountType::where('code', 'CURRENT')->first();
        $savingsType = AccountType::where('code', 'SAVINGS')->first() ?? $currentType;
        $usdType = AccountType::where('code', 'USD-CURRENT')->first() ?? $currentType;
        $businessType = AccountType::where('code', 'BUSINESS')->first() ?? $currentType;
        $studentType = AccountType::where('code', 'STUDENT')->first() ?? $currentType;

        $customers = Customer::all()->keyBy('customer_number');

        $accountsData = [
            // Customer 1 - Ahmad Al-Sayed
            [
                'customer_number' => 'CUST-000001',
                'account_type_id' => $currentType->id,
                'account_number' => '1000100001',
                'iban' => 'SY001000000000001000100001',
                'status' => AccountStatus::Open,
                'balance' => 14500000.00,
                'currency' => 'SYP',
                'opened_at' => now()->subYears(2),
            ],
            [
                'customer_number' => 'CUST-000001',
                'account_type_id' => $savingsType->id,
                'account_number' => '1000100002',
                'iban' => 'SY001000000000001000100002',
                'status' => AccountStatus::Open,
                'balance' => 35000000.00,
                'currency' => 'SYP',
                'opened_at' => now()->subYear(),
            ],
            [
                'customer_number' => 'CUST-000001',
                'account_type_id' => $usdType->id,
                'account_number' => '1000100003',
                'iban' => 'SY001000000000001000100003',
                'status' => AccountStatus::Open,
                'balance' => 12450.00,
                'currency' => 'USD',
                'opened_at' => now()->subMonths(6),
            ],

            // Customer 2 - Layla Mansour
            [
                'customer_number' => 'CUST-000002',
                'account_type_id' => $currentType->id,
                'account_number' => '1000200001',
                'iban' => 'SY001000000000001000200001',
                'status' => AccountStatus::Open,
                'balance' => 8200000.00,
                'currency' => 'SYP',
                'opened_at' => now()->subMonths(8),
            ],
            [
                'customer_number' => 'CUST-000002',
                'account_type_id' => $usdType->id,
                'account_number' => '1000200002',
                'iban' => 'SY001000000000001000200002',
                'status' => AccountStatus::Open,
                'balance' => 24800.00,
                'currency' => 'USD',
                'opened_at' => now()->subMonths(5),
            ],

            // Customer 3 - Omar Khaled
            [
                'customer_number' => 'CUST-000003',
                'account_type_id' => $currentType->id,
                'account_number' => '1000300001',
                'iban' => 'SY001000000000001000300001',
                'status' => AccountStatus::Open,
                'balance' => 65000000.00,
                'currency' => 'SYP',
                'opened_at' => now()->subYears(3),
            ],
            [
                'customer_number' => 'CUST-000003',
                'account_type_id' => $businessType->id,
                'account_number' => '1000300002',
                'iban' => 'SY001000000000001000300002',
                'status' => AccountStatus::Open,
                'balance' => 120000000.00,
                'currency' => 'SYP',
                'opened_at' => now()->subYears(2),
            ],

            // Customer 4 - Nour Al-Hassan
            [
                'customer_number' => 'CUST-000004',
                'account_type_id' => $currentType->id,
                'account_number' => '1000400001',
                'iban' => 'SY001000000000001000400001',
                'status' => AccountStatus::Open,
                'balance' => 5600000.00,
                'currency' => 'SYP',
                'opened_at' => now()->subMonths(10),
            ],
            [
                'customer_number' => 'CUST-000004',
                'account_type_id' => $savingsType->id,
                'account_number' => '1000400002',
                'iban' => 'SY001000000000001000400002',
                'status' => AccountStatus::Open,
                'balance' => 18000000.00,
                'currency' => 'SYP',
                'opened_at' => now()->subMonths(4),
            ],

            // Customer 5 - Tariq Al-Halabi
            [
                'customer_number' => 'CUST-000005',
                'account_type_id' => $currentType->id,
                'account_number' => '1000500001',
                'iban' => 'SY001000000000001000500001',
                'status' => AccountStatus::Open,
                'balance' => 9800000.00,
                'currency' => 'SYP',
                'opened_at' => now()->subMonths(7),
            ],

            // Customer 6 - Maya Haddad
            [
                'customer_number' => 'CUST-000006',
                'account_type_id' => $currentType->id,
                'account_number' => '1000600001',
                'iban' => 'SY001000000000001000600001',
                'status' => AccountStatus::Open,
                'balance' => 7300000.00,
                'currency' => 'SYP',
                'opened_at' => now()->subMonths(3),
            ],

            // Customer 7 - Ziad Kassam
            [
                'customer_number' => 'CUST-000007',
                'account_type_id' => $currentType->id,
                'account_number' => '1000700001',
                'iban' => 'SY001000000000001000700001',
                'status' => AccountStatus::Open,
                'balance' => 3400000.00,
                'currency' => 'SYP',
                'opened_at' => now()->subMonths(2),
            ],

            // Customer 8 - Karim Nasser
            [
                'customer_number' => 'CUST-000008',
                'account_type_id' => $studentType->id,
                'account_number' => '1000800001',
                'iban' => 'SY001000000000001000800001',
                'status' => AccountStatus::Open,
                'balance' => 750000.00,
                'currency' => 'SYP',
                'opened_at' => now()->subMonth(),
            ],
        ];

        foreach ($accountsData as $acc) {
            $customer = $customers->get($acc['customer_number']);
            if (!$customer) {
                continue;
            }

            Account::updateOrCreate(
                ['account_number' => $acc['account_number']],
                [
                    'customer_id' => $customer->id,
                    'account_type_id' => $acc['account_type_id'],
                    'iban' => $acc['iban'],
                    'status' => $acc['status'],
                    'balance' => $acc['balance'],
                    'currency' => $acc['currency'],
                    'opened_at' => $acc['opened_at'],
                ]
            );
        }
    }
}

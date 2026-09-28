<?php

namespace Database\Seeders;

use App\Modules\AccountTypes\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'CURRENT',
                'name' => 'Current Account (SYP)',
                'description' => 'Standard everyday checking account for domestic daily banking operations and payroll.',
                'currency' => 'SYP',
                'status' => 'active',
            ],
            [
                'code' => 'SAVINGS',
                'name' => 'Savings Account (SYP)',
                'description' => 'Interest-earning savings account with easy deposits and monthly returns.',
                'currency' => 'SYP',
                'status' => 'active',
            ],
            [
                'code' => 'USD-CURRENT',
                'name' => 'USD Current Account',
                'description' => 'Foreign currency account held in US Dollars for international trade and wire transfers.',
                'currency' => 'USD',
                'status' => 'active',
            ],
            [
                'code' => 'EUR-CURRENT',
                'name' => 'EUR Current Account',
                'description' => 'Foreign currency checking account denominated in Euros.',
                'currency' => 'EUR',
                'status' => 'active',
            ],
            [
                'code' => 'BUSINESS',
                'name' => 'Business Corporate Account',
                'description' => 'High-capacity account for commercial enterprises and institutional banking.',
                'currency' => 'SYP',
                'status' => 'active',
            ],
            [
                'code' => 'STUDENT',
                'name' => 'Student & Youth Account',
                'description' => 'Zero-maintenance fee banking account designed for university students.',
                'currency' => 'SYP',
                'status' => 'active',
            ],
        ];

        foreach ($types as $typeData) {
            AccountType::updateOrCreate(
                ['code' => $typeData['code']],
                $typeData
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Modules\LoanTypes\Models\LoanType;
use Illuminate\Database\Seeder;

class LoanTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'code' => 'PERSONAL',
                'name' => 'Personal Consumer Loan',
                'description' => 'Fast financing for personal expenditures, medical emergencies, and home upgrades.',
                'currency' => 'SYP',
                'minimum_amount' => 500000.00,
                'maximum_amount' => 50000000.00,
                'minimum_term_months' => 6,
                'maximum_term_months' => 60,
                'annual_interest_rate' => 12.00,
                'interest_method' => 'reducing_balance',
                'repayment_frequency' => 'monthly',
                'requires_collateral' => false,
                'status' => 'active',
            ],
            [
                'code' => 'BUSINESS',
                'name' => 'Commercial Business Loan',
                'description' => 'Working capital and operational expansion facilities for enterprises.',
                'currency' => 'SYP',
                'minimum_amount' => 5000000.00,
                'maximum_amount' => 500000000.00,
                'minimum_term_months' => 12,
                'maximum_term_months' => 84,
                'annual_interest_rate' => 10.50,
                'interest_method' => 'reducing_balance',
                'repayment_frequency' => 'monthly',
                'requires_collateral' => true,
                'status' => 'active',
            ],
            [
                'code' => 'VEHICLE',
                'name' => 'Auto & Vehicle Loan',
                'description' => 'Financing program for purchasing new or certified used commercial and private vehicles.',
                'currency' => 'SYP',
                'minimum_amount' => 2000000.00,
                'maximum_amount' => 150000000.00,
                'minimum_term_months' => 12,
                'maximum_term_months' => 72,
                'annual_interest_rate' => 11.25,
                'interest_method' => 'reducing_balance',
                'repayment_frequency' => 'monthly',
                'requires_collateral' => true,
                'status' => 'active',
            ],
            [
                'code' => 'MORTGAGE',
                'name' => 'Residential Housing Mortgage',
                'description' => 'Long-term residential real estate financing with subsidized competitive rates.',
                'currency' => 'SYP',
                'minimum_amount' => 20000000.00,
                'maximum_amount' => 1000000000.00,
                'minimum_term_months' => 60,
                'maximum_term_months' => 240,
                'annual_interest_rate' => 8.75,
                'interest_method' => 'reducing_balance',
                'repayment_frequency' => 'monthly',
                'requires_collateral' => true,
                'status' => 'active',
            ],
            [
                'code' => 'EDUCATION',
                'name' => 'Higher Education Financing',
                'description' => 'Low-interest educational tuition loans for university and postgraduate students.',
                'currency' => 'SYP',
                'minimum_amount' => 1000000.00,
                'maximum_amount' => 30000000.00,
                'minimum_term_months' => 6,
                'maximum_term_months' => 48,
                'annual_interest_rate' => 7.50,
                'interest_method' => 'reducing_balance',
                'repayment_frequency' => 'monthly',
                'requires_collateral' => false,
                'status' => 'active',
            ],
        ];

        foreach ($types as $typeData) {
            LoanType::updateOrCreate(
                ['code' => $typeData['code']],
                $typeData
            );
        }
    }
}

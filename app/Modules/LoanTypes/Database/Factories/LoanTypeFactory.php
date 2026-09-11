<?php

namespace App\Modules\LoanTypes\Database\Factories;

use App\Modules\LoanTypes\Enums\LoanTypeStatus;
use App\Modules\LoanTypes\Models\LoanType;
use App\Modules\Loans\Enums\InterestMethod;
use App\Modules\Loans\Enums\RepaymentFrequency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoanType>
 */
class LoanTypeFactory extends Factory
{
    protected $model = LoanType::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('LOAN-##'),
            'name' => 'Test Loan Type',
            'description' => $this->faker->sentence(),
            'currency' => 'SYP',
            'minimum_amount' => 1000,
            'maximum_amount' => 1000000,
            'minimum_term_months' => 6,
            'maximum_term_months' => 60,
            'annual_interest_rate' => 12,
            'interest_method' => InterestMethod::ReducingBalance,
            'repayment_frequency' => RepaymentFrequency::Monthly,
            'requires_collateral' => false,
            'status' => LoanTypeStatus::Active,
        ];
    }
}

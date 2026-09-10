<?php

namespace App\Modules\Loans\Database\Factories;

use App\Modules\Accounts\Models\Account;
use App\Modules\Customers\Database\Factories\CustomerFactory;
use App\Modules\LoanTypes\Database\Factories\LoanTypeFactory;
use App\Modules\Loans\Enums\InterestMethod;
use App\Modules\Loans\Enums\LoanStatus;
use App\Modules\Loans\Enums\RepaymentFrequency;
use App\Modules\Loans\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    protected $model = Loan::class;

    public function definition(): array
    {
        return [
            'loan_reference' => 'LN-'.date('Y').'-'.$this->faker->unique()->numerify('######'),
            'customer_id' => CustomerFactory::new(),
            'account_id' => Account::factory(),
            'loan_type_id' => LoanTypeFactory::new(),
            'requested_amount' => 10000,
            'approved_amount' => null,
            'disbursed_amount' => null,
            'annual_interest_rate' => 12,
            'term_months' => 12,
            'interest_method' => InterestMethod::ReducingBalance,
            'repayment_frequency' => RepaymentFrequency::Monthly,
            'purpose' => 'Working capital',
            'status' => LoanStatus::Pending,
            'application_date' => today(),
            'approved_at' => null,
            'approved_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
            'disbursed_at' => null,
            'first_payment_date' => null,
            'total_interest' => 0,
            'total_payable' => 0,
            'total_paid' => 0,
            'outstanding_principal' => 0,
            'outstanding_interest' => 0,
            'next_payment_date' => null,
        ];
    }
}

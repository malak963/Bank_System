<?php

namespace App\Modules\Installments\Database\Factories;

use App\Modules\Installments\Enums\InstallmentStatus;
use App\Modules\Installments\Models\Installment;
use App\Modules\Loans\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Installment>
 */
class InstallmentFactory extends Factory
{
    protected $model = Installment::class;

    public function definition(): array
    {
        return [
            'loan_id' => Loan::factory(),
            'installment_number' => 1,
            'due_date' => today()->addMonth(),
            'principal_amount' => 1000,
            'interest_amount' => 100,
            'amount_due' => 1100,
            'principal_paid' => 0,
            'interest_paid' => 0,
            'amount_paid' => 0,
            'status' => InstallmentStatus::Pending,
            'paid_at' => null,
            'last_payment_at' => null,
        ];
    }
}

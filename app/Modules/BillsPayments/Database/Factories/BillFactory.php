<?php

namespace App\Modules\BillsPayments\Database\Factories;

use App\Modules\BillsPayments\Enums\BillStatus;
use App\Modules\BillsPayments\Enums\BillType;
use App\Modules\BillsPayments\Models\Bill;
use Illuminate\Database\Eloquent\Factories\Factory;

class BillFactory extends Factory
{
    protected $model = Bill::class;

    public function definition(): array
    {
        return [
            'bill_reference' => 'BILL-' . strtoupper(uniqid()),
            'customer_id' => \App\Modules\Customers\Models\Customer::factory(),
            'account_id' => \App\Modules\Accounts\Models\Account::factory(),
            'bill_type' => fake()->randomElement(BillType::cases()),
            'provider_name' => fake()->company(),
            'provider_account_number' => fake()->numerify('#########'),
            'amount' => fake()->randomFloat(2, 1000, 50000),
            'currency' => 'SYP',
            'due_date' => fake()->dateTimeBetween('+1 week', '+1 month'),
            'status' => BillStatus::PENDING,
            'description' => fake()->sentence(),
            'metadata' => [
                'invoice_number' => fake()->numerify('INV-########'),
                'period' => fake()->monthName() . ' ' . fake()->year(),
            ],
        ];
    }

    public function pending(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => BillStatus::PENDING,
        ]);
    }

    public function scheduled(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => BillStatus::SCHEDULED,
        ]);
    }

    public function completed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => BillStatus::COMPLETED,
            'paid_at' => now(),
        ]);
    }

    public function failed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => BillStatus::FAILED,
            'failed_at' => now(),
            'failed_reason' => 'Insufficient funds',
        ]);
    }

    public function overdue(): self
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => now()->subDays(5),
            'status' => BillStatus::PENDING,
        ]);
    }
}

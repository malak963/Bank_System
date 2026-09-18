<?php

namespace App\Modules\Products\Database\Factories;

use App\Modules\Accounts\Models\Account;
use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Models\Customer;
use App\Modules\Products\Enums\ProductStatus;
use App\Modules\Products\Enums\ProductType;
use App\Modules\Products\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $productType = $this->faker->randomElement(ProductType::cases());
        $status = $this->faker->randomElement(ProductStatus::cases());
        
        return [
            'product_number' => 'PRD' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'customer_id' => Customer::factory(),
            'account_id' => Account::factory(),
            'branch_id' => Branch::factory(),
            'product_type' => $productType,
            'status' => $status,
            'name' => $this->faker->randomElement(['Premium Savings', 'Fixed Deposit 12M', 'Investment Plus', 'Retirement Gold', 'Education Fund']) . ' - ' . $this->faker->word(),
            'balance' => $this->faker->randomFloat(2, 1000, 100000),
            'currency' => 'USD',
            'interest_rate' => $this->faker->randomFloat(2, 1, 10),
            'term_months' => $this->faker->randomNumber(2),
            'maturity_date' => $this->faker->optional(0.7)->dateTimeBetween('+1 month', '+5 years'),
            'auto_renew' => $this->faker->boolean(30),
            'min_balance' => $productType->minBalance(),
            'max_balance' => $this->faker->optional()->randomFloat(2, 100000, 1000000),
            'interest_earned' => $this->faker->randomFloat(2, 0, 5000),
            'last_interest_calculation' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'opened_at' => $this->faker->dateTimeBetween('-2 years', '-1 month'),
            'closed_at' => $status === ProductStatus::Closed ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
            'notes' => $this->faker->optional()->paragraph(),
            'metadata' => $this->faker->optional()->randomElements([
                'risk_level' => $this->faker->randomElement(['low', 'medium', 'high']),
                'tax_exempt' => $this->faker->boolean(),
                'beneficiary' => $this->faker->name(),
            ], $this->faker->numberBetween(0, 3)),
        ];
    }

    public function savingsAccount(): self
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => ProductType::SavingsAccount,
            'name' => 'Savings Account',
            'interest_rate' => 2.5,
            'term_months' => null,
            'maturity_date' => null,
        ]);
    }

    public function fixedDeposit(): self
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => ProductType::FixedDeposit,
            'name' => 'Fixed Deposit',
            'interest_rate' => 5.0,
            'term_months' => 12,
            'maturity_date' => now()->addMonths(12),
        ]);
    }

    public function investmentAccount(): self
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => ProductType::InvestmentAccount,
            'name' => 'Investment Account',
            'interest_rate' => 7.5,
            'term_months' => null,
            'maturity_date' => null,
        ]);
    }

    public function certificateOfDeposit(): self
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => ProductType::CertificateOfDeposit,
            'name' => 'Certificate of Deposit',
            'interest_rate' => 4.5,
            'term_months' => 6,
            'maturity_date' => now()->addMonths(6),
        ]);
    }

    public function matured(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductStatus::Matured,
            'maturity_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function closed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductStatus::Closed,
            'closed_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function active(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProductStatus::Active,
        ]);
    }
}

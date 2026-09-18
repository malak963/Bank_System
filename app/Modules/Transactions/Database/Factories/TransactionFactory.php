<?php

namespace App\Modules\Transactions\Database\Factories;

use App\Modules\Accounts\Models\Account;
use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Models\Customer;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Enums\TransactionType;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 10, 10000);
        $balanceBefore = $this->faker->randomFloat(2, 1000, 50000);
        $transactionType = $this->faker->randomElement(TransactionType::cases());
        
        $balanceAfter = $transactionType->isCredit() 
            ? $balanceBefore + $amount 
            : $balanceBefore - $amount;

        return [
            'transaction_reference' => 'TXN' . $this->faker->unique()->numerify('##########'),
            'branch_id' => Branch::factory(),
            'customer_id' => Customer::factory(),
            'account_id' => Account::factory(),
            'transaction_type' => $transactionType,
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
            'currency' => 'USD',
            'status' => TransactionStatus::Completed,
            'description' => $this->faker->sentence(),
            'reference_number' => $this->faker->optional()->numerify('REF########'),
            'fees' => $this->faker->optional(0.3)->randomFloat(2, 0, 50),
            'tax' => $this->faker->optional(0.2)->randomFloat(2, 0, 20),
            'category' => $this->faker->optional()->word(),
            'sub_category' => $this->faker->optional()->word(),
            'tags' => $this->faker->optional()->randomElements(['urgent', 'important', 'routine'], $this->faker->numberBetween(0, 3)),
            'metadata' => $this->faker->optional()->randomElements([
                'location' => $this->faker->city(),
                'channel' => $this->faker->randomElement(['mobile', 'web', 'atm', 'branch']),
                'device' => $this->faker->word(),
            ], $this->faker->numberBetween(0, 3)),
            'processed_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'ip_address' => $this->faker->optional()->ipv4(),
            'user_agent' => $this->faker->optional()->userAgent(),
            'device_id' => $this->faker->optional()->uuid(),
        ];
    }

    public function deposit(): self
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => TransactionType::Deposit,
            'description' => 'Cash deposit',
        ]);
    }

    public function withdrawal(): self
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => TransactionType::Withdrawal,
            'description' => 'Cash withdrawal',
        ]);
    }

    public function transfer(): self
    {
        return $this->state(fn (array $attributes) => [
            'transaction_type' => TransactionType::Transfer,
            'description' => 'Transfer to another account',
        ]);
    }

    public function pending(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatus::Pending,
            'processed_at' => null,
        ]);
    }

    public function failed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatus::Failed,
            'failed_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'failed_reason' => $this->faker->randomElement([
                'Insufficient funds',
                'Account frozen',
                'Invalid recipient',
                'Network error',
                'Security check failed',
            ]),
        ]);
    }

    public function reversed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatus::Reversed,
            'reversed_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'reversal_reason' => $this->faker->randomElement([
                'Customer request',
                'Error correction',
                'Fraud detected',
                'System error',
            ]),
        ]);
    }
}

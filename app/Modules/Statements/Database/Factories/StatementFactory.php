<?php

namespace App\Modules\Statements\Database\Factories;

use App\Modules\Statements\Models\Statement;
use Illuminate\Database\Eloquent\Factories\Factory;

class StatementFactory extends Factory
{
    protected $model = Statement::class;

    public function definition(): array
    {
        $periodStart = fake()->dateTimeBetween('-3 months', '-1 month');
        $periodEnd = fake()->dateTimeBetween('-1 month', 'now');
        
        return [
            'statement_reference' => 'STMT-' . strtoupper(uniqid()),
            'account_id' => \App\Modules\Accounts\Models\Account::factory(),
            'customer_id' => \App\Modules\Customers\Models\Customer::factory(),
            'period_start' => $periodStart->format('Y-m-d'),
            'period_end' => $periodEnd->format('Y-m-d'),
            'opening_balance' => fake()->randomFloat(2, 1000, 100000),
            'closing_balance' => fake()->randomFloat(2, 1000, 100000),
            'total_debits' => fake()->randomFloat(2, 0, 50000),
            'total_credits' => fake()->randomFloat(2, 0, 50000),
            'transaction_count' => fake()->numberBetween(5, 100),
            'currency' => 'SYP',
            'status' => 'completed',
            'generated_at' => now(),
        ];
    }
}

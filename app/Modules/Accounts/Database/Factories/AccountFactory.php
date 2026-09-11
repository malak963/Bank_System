<?php

namespace App\Modules\Accounts\Database\Factories;

use App\Modules\AccountTypes\Models\AccountType;
use App\Modules\Accounts\Enums\AccountStatus;
use App\Modules\Accounts\Models\Account;
use App\Modules\Customers\Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        $accountNumber = '10'.str_pad((string) $this->faker->unique()->numberBetween(0, 9999999999), 10, '0', STR_PAD_LEFT);

        return [
            'customer_id' => CustomerFactory::new(),
            'account_type_id' => AccountType::query()->inRandomOrder()->value('id') ?? AccountType::factory(),
            'account_number' => $accountNumber,
            'iban' => 'SY00'.'1000'.'000'.str_pad($accountNumber, 15, '0', STR_PAD_LEFT),
            'status' => AccountStatus::Open,
            'balance' => 0,
            'opened_at' => now(),
            'closed_at' => null,
            'frozen_at' => null,
            'freeze_reason' => null,
        ];
    }
}

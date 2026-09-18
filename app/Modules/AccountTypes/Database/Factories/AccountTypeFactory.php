<?php

namespace App\Modules\AccountTypes\Database\Factories;

use App\Modules\AccountTypes\Enums\AccountTypeStatus;
use App\Modules\AccountTypes\Models\AccountType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccountType>
 */
class AccountTypeFactory extends Factory
{
    protected $model = AccountType::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('TYPE-##'),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'currency' => 'SYP',
            'status' => AccountTypeStatus::Active,
        ];
    }
}

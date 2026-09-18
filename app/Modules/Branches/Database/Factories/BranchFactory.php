<?php

namespace App\Modules\Branches\Database\Factories;

use App\Modules\Branches\Enums\BranchStatus;
use App\Modules\Branches\Models\Branch;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'code' => 'BR'.str_pad((string) $this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->companyEmail(),
            'manager_id' => User::factory(),
            'latitude' => $this->faker->latitude(33.5, 34.5), // Syria coordinates
            'longitude' => $this->faker->longitude(35.5, 38.5),
            'status' => BranchStatus::Open,
            'opened_at' => now(),
            'closed_at' => null,
        ];
    }
}

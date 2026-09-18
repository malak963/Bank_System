<?php

namespace App\Modules\CustomerService\Database\Factories;

use App\Modules\CustomerService\Models\Ticket;
use App\Modules\CustomerService\Models\TicketResponse;
use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketResponseFactory extends Factory
{
    protected $model = TicketResponse::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => \App\Models\User::factory(),
            'customer_id' => Customer::factory(),
            'message' => $this->faker->paragraph(),
            'is_internal' => $this->faker->boolean(20),
            'attachments' => $this->faker->optional()->randomElements([
                'filename' => $this->faker->word() . '.pdf',
                'url' => $this->faker->url(),
            ], $this->faker->numberBetween(0, 2)),
            'metadata' => $this->faker->optional()->randomElements([
                'source' => $this->faker->randomElement(['web', 'mobile', 'email']),
                'device' => $this->faker->word(),
            ], $this->faker->numberBetween(0, 2)),
        ];
    }

    public function public(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_internal' => false,
        ]);
    }

    public function internal(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_internal' => true,
        ]);
    }

    public function fromCustomer(): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'customer_id' => Customer::factory(),
            'is_internal' => false,
        ]);
    }

    public function fromAgent(): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => \App\Models\User::factory(),
            'customer_id' => null,
            'is_internal' => false,
        ]);
    }
}

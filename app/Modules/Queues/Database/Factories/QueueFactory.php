<?php

namespace App\Modules\Queues\Database\Factories;

use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Models\Customer;
use App\Modules\Queues\Enums\QueuePriority;
use App\Modules\Queues\Enums\QueueStatus;
use App\Modules\Queues\Models\Queue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Queue>
 */
class QueueFactory extends Factory
{
    protected $model = Queue::class;

    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'customer_id' => Customer::factory(),
            'ticket_number' => 'A' . str_pad((string) fake()->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'service_type' => fake()->randomElement(['general', 'loan_consultation', 'account_opening', 'wealth_management', 'card_services', 'complaint_resolution']),
            'status' => QueueStatus::Waiting,
            'priority' => fake()->randomElement(QueuePriority::cases()),
            'joined_at' => now(),
            'called_at' => null,
            'serving_at' => null,
            'completed_at' => null,
            'estimated_wait_time' => fake()->numberBetween(5, 30),
            'actual_wait_time' => null,
            'service_counter' => fake()->randomElement(['C1', 'C2', 'C3', 'C4']),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function serving(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => QueueStatus::Serving,
            'called_at' => fake()->dateTimeBetween('-30 minutes', 'now'),
            'serving_at' => fake()->dateTimeBetween('-30 minutes', 'now'),
            'actual_wait_time' => fake()->numberBetween(5, 25),
        ]);
    }

    public function completed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => QueueStatus::Completed,
            'called_at' => fake()->dateTimeBetween('-1 hour', '-30 minutes'),
            'serving_at' => fake()->dateTimeBetween('-1 hour', '-30 minutes'),
            'completed_at' => fake()->dateTimeBetween('-30 minutes', 'now'),
            'actual_wait_time' => fake()->numberBetween(5, 25),
        ]);
    }

    public function highPriority(): self
    {
        return $this->state(fn (array $attributes) => [
            'priority' => QueuePriority::High,
        ]);
    }
}

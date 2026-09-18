<?php

namespace App\Modules\Appointments\Database\Factories;

use App\Modules\Appointments\Enums\AppointmentStatus;
use App\Modules\Appointments\Enums\ServiceType;
use App\Modules\Appointments\Models\Appointment;
use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $serviceType = fake()->randomElement(ServiceType::cases());
        
        return [
            'branch_id' => Branch::factory(),
            'customer_id' => Customer::factory(),
            'service_type' => $serviceType,
            'status' => AppointmentStatus::Pending,
            'appointment_date' => fake()->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
            'appointment_time' => fake()->dateTimeBetween('+1 day', '+30 days'),
            'estimated_duration' => $serviceType->estimatedDuration(),
            'notes' => fake()->optional()->sentence(),
            'confirmation_sent_at' => fake()->optional()->dateTime(),
            'reminder_sent_at' => null,
            'checked_in_at' => null,
            'started_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
            'cancellation_reason' => null,
        ];
    }

    public function confirmed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::Confirmed,
            'confirmation_sent_at' => now(),
        ]);
    }

    public function completed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => AppointmentStatus::Completed,
            'checked_in_at' => fake()->dateTimeBetween('-2 hours', 'now'),
            'started_at' => fake()->dateTimeBetween('-2 hours', '-1 hour'),
            'completed_at' => fake()->dateTimeBetween('-1 hour', 'now'),
        ]);
    }
}

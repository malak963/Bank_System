<?php

namespace App\Modules\CustomerService\Database\Factories;

use App\Modules\Branches\Models\Branch;
use App\Modules\CustomerService\Enums\TicketCategory;
use App\Modules\CustomerService\Enums\TicketPriority;
use App\Modules\CustomerService\Enums\TicketStatus;
use App\Modules\CustomerService\Models\Ticket;
use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $category = $this->faker->randomElement(TicketCategory::cases());
        $priority = $category->defaultPriority();
        $status = $this->faker->randomElement(TicketStatus::cases());
        
        return [
            'ticket_number' => 'TKT' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'customer_id' => Customer::factory(),
            'branch_id' => Branch::factory(),
            'category' => $category,
            'priority' => $priority,
            'status' => $status,
            'subject' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'assigned_to' => \App\Models\User::factory(),
            'resolved_by' => $status === TicketStatus::Resolved ? \App\Models\User::factory() : null,
            'resolution' => $status === TicketStatus::Resolved ? $this->faker->sentence() : null,
            'resolved_at' => $status === TicketStatus::Resolved ? $this->faker->dateTimeBetween('-1 week', 'now') : null,
            'closed_at' => $status === TicketStatus::Closed ? $this->faker->dateTimeBetween('-1 week', 'now') : null,
            'first_response_at' => $status !== TicketStatus::Open ? $this->faker->dateTimeBetween('-1 week', 'now') : null,
            'escalated_at' => $status === TicketStatus::Escalated ? $this->faker->dateTimeBetween('-1 week', 'now') : null,
            'escalated_to' => $status === TicketStatus::Escalated ? \App\Models\User::factory() : null,
            'customer_satisfaction' => $status === TicketStatus::Closed ? $this->faker->numberBetween(1, 5) : null,
            'tags' => $this->faker->optional()->randomElements(['urgent', 'important', 'routine', 'follow-up'], $this->faker->numberBetween(0, 3)),
            'metadata' => $this->faker->optional()->randomElements([
                'source' => $this->faker->randomElement(['web', 'mobile', 'email', 'phone', 'branch']),
                'contact_method' => $this->faker->randomElement(['email', 'phone', 'chat']),
                'language' => $this->faker->randomElement(['en', 'ar', 'fr']),
            ], $this->faker->numberBetween(0, 3)),
        ];
    }

    public function account(): self
    {
        return $this->state(fn (array $attributes) => [
            'category' => TicketCategory::Account,
            'subject' => 'Account related issue',
        ]);
    }

    public function transaction(): self
    {
        return $this->state(fn (array $attributes) => [
            'category' => TicketCategory::Transaction,
            'subject' => 'Transaction issue',
        ]);
    }

    public function fraud(): self
    {
        return $this->state(fn (array $attributes) => [
            'category' => TicketCategory::Fraud,
            'priority' => TicketPriority::Critical,
            'subject' => 'Fraud report',
        ]);
    }

    public function complaint(): self
    {
        return $this->state(fn (array $attributes) => [
            'category' => TicketCategory::Complaint,
            'priority' => TicketPriority::High,
            'subject' => 'Customer complaint',
        ]);
    }

    public function open(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Open,
            'first_response_at' => null,
        ]);
    }

    public function resolved(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Resolved,
            'resolved_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'resolution' => $this->faker->sentence(),
        ]);
    }

    public function urgent(): self
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TicketPriority::Urgent,
        ]);
    }

    public function critical(): self
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TicketPriority::Critical,
        ]);
    }
}

<?php

namespace App\Modules\Notifications\Database\Factories;

use App\Modules\Customers\Models\Customer;
use App\Modules\Notifications\Enums\NotificationChannel;
use App\Modules\Notifications\Enums\NotificationStatus;
use App\Modules\Notifications\Enums\NotificationType;
use App\Modules\Notifications\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        $notificationType = $this->faker->randomElement(NotificationType::cases());
        $channel = $this->faker->randomElement(NotificationChannel::cases());
        $status = $this->faker->randomElement(NotificationStatus::cases());
        
        return [
            'customer_id' => Customer::factory(),
            'notification_type' => $notificationType,
            'channel' => $channel,
            'status' => $status,
            'subject' => $this->faker->optional()->sentence(),
            'message' => $this->generateMessage($notificationType),
            'data' => $this->faker->optional()->randomElements([
                'transaction_id' => $this->faker->numerify('TXN########'),
                'amount' => $this->faker->randomFloat(2, 10, 1000),
                'account_number' => $this->faker->numerify('############'),
                'card_number' => '**** **** **** ' . $this->faker->numerify('####'),
            ], $this->faker->numberBetween(0, 3)),
            'scheduled_at' => $this->faker->optional(0.3)->dateTimeBetween('+1 hour', '+1 week'),
            'sent_at' => $status === NotificationStatus::Sent || $status === NotificationStatus::Delivered || $status === NotificationStatus::Read ? $this->faker->dateTimeBetween('-1 hour', 'now') : null,
            'delivered_at' => $status === NotificationStatus::Delivered || $status === NotificationStatus::Read ? $this->faker->dateTimeBetween('-30 minutes', 'now') : null,
            'read_at' => $status === NotificationStatus::Read ? $this->faker->dateTimeBetween('-15 minutes', 'now') : null,
            'failed_at' => $status === NotificationStatus::Failed ? $this->faker->dateTimeBetween('-1 hour', 'now') : null,
            'failure_reason' => $status === NotificationStatus::Failed ? $this->faker->randomElement([
                'Invalid email address',
                'SMS delivery failed',
                'Push notification rejected',
                'Rate limit exceeded',
                'Service unavailable',
            ]) : null,
            'retry_count' => $status === NotificationStatus::Failed ? $this->faker->numberBetween(1, 3) : 0,
            'priority' => $notificationType->isUrgent() ? $this->faker->numberBetween(8, 10) : $this->faker->numberBetween(1, 7),
            'metadata' => $this->faker->optional()->randomElements([
                'campaign' => $this->faker->word(),
                'template' => $this->faker->word(),
                'source' => $this->faker->randomElement(['web', 'mobile', 'api', 'system']),
            ], $this->faker->numberBetween(0, 3)),
        ];
    }

    public function transaction(): self
    {
        return $this->state(fn (array $attributes) => [
            'notification_type' => NotificationType::Transaction,
            'subject' => 'Transaction Alert',
            'message' => 'Your account has been debited with $' . $this->faker->randomFloat(2, 10, 1000),
        ]);
    }

    public function security(): self
    {
        return $this->state(fn (array $attributes) => [
            'notification_type' => NotificationType::Security,
            'subject' => 'Security Alert',
            'message' => 'New login detected from a new device',
            'priority' => 10,
        ]);
    }

    public function email(): self
    {
        return $this->state(fn (array $attributes) => [
            'channel' => NotificationChannel::Email,
        ]);
    }

    public function sms(): self
    {
        return $this->state(fn (array $attributes) => [
            'channel' => NotificationChannel::SMS,
        ]);
    }

    public function push(): self
    {
        return $this->state(fn (array $attributes) => [
            'channel' => NotificationChannel::Push,
        ]);
    }

    public function pending(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => NotificationStatus::Pending,
            'sent_at' => null,
            'delivered_at' => null,
            'read_at' => null,
            'failed_at' => null,
        ]);
    }

    public function delivered(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => NotificationStatus::Delivered,
            'sent_at' => $this->faker->dateTimeBetween('-1 hour', '-30 minutes'),
            'delivered_at' => $this->faker->dateTimeBetween('-30 minutes', 'now'),
        ]);
    }

    public function failed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => NotificationStatus::Failed,
            'failed_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
            'failure_reason' => $this->faker->randomElement([
                'Invalid recipient',
                'Service unavailable',
                'Rate limit exceeded',
            ]),
            'retry_count' => $this->faker->numberBetween(1, 3),
        ]);
    }

    private function generateMessage(NotificationType $type): string
    {
        return match ($type) {
            NotificationType::Transaction => 'Your account has been ' . $this->faker->randomElement(['credited', 'debited']) . ' with $' . $this->faker->randomFloat(2, 10, 1000),
            NotificationType::Account => 'Your account balance is now $' . $this->faker->randomFloat(2, 100, 10000),
            NotificationType::Card => 'Your card has been ' . $this->faker->randomElement(['used', 'blocked', 'activated']),
            NotificationType::Loan => 'Your loan payment of $' . $this->faker->randomFloat(2, 100, 1000) . ' has been processed',
            NotificationType::Security => 'Security alert: ' . $this->faker->randomElement(['New login detected', 'Password changed', 'Account locked']),
            NotificationType::Marketing => 'Special offer: ' . $this->faker->sentence(),
            NotificationType::System => 'System maintenance scheduled for ' . $this->faker->dateTimeBetween('+1 day', '+1 week')->format('M d, Y'),
            NotificationType::Appointment => 'Your appointment is scheduled for ' . $this->faker->dateTimeBetween('+1 day', '+1 week')->format('M d, Y H:i'),
            NotificationType::Payment => 'Your payment of $' . $this->faker->randomFloat(2, 10, 500) . ' has been processed',
            NotificationType::Balance => 'Your current balance is $' . $this->faker->randomFloat(2, 100, 10000),
        };
    }
}

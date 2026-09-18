<?php

namespace App\Modules\Security\Database\Factories;

use App\Modules\Customers\Models\Customer;
use App\Modules\Security\Enums\SecurityEventType;
use App\Modules\Security\Enums\SecurityLevel;
use App\Modules\Security\Models\SecurityEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class SecurityEventFactory extends Factory
{
    protected $model = SecurityEvent::class;

    public function definition(): array
    {
        $eventType = $this->faker->randomElement(SecurityEventType::cases());
        $securityLevel = $this->determineSecurityLevel($eventType);
        
        return [
            'event_type' => $eventType,
            'security_level' => $securityLevel,
            'user_id' => \App\Models\User::factory(),
            'customer_id' => Customer::factory(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'device_id' => $this->faker->optional()->uuid(),
            'location' => $this->faker->optional()->city(),
            'description' => $this->generateDescription($eventType),
            'details' => $this->faker->optional()->randomElements([
                'attempt_count' => $this->faker->numberBetween(1, 10),
                'success' => $this->faker->boolean(),
                'method' => $this->faker->randomElement(['POST', 'GET', 'PUT', 'DELETE']),
                'endpoint' => $this->faker->url(),
                'transaction_amount' => $this->faker->randomFloat(2, 100, 10000),
            ], $this->faker->numberBetween(0, 4)),
            'source' => $this->faker->randomElement(['web', 'mobile', 'api', 'system']),
            'is_resolved' => $this->faker->boolean(70),
            'resolved_at' => $this->faker->optional(0.7)->dateTimeBetween('-1 week', 'now'),
            'resolved_by' => \App\Models\User::factory(),
            'resolution_notes' => $this->faker->optional()->sentence(),
            'action_taken' => $this->faker->optional()->randomElement(['blocked', 'monitored', 'notified', 'investigated']),
            'blocked' => $this->faker->boolean(20),
            'blocked_until' => $this->faker->optional(0.2)->dateTimeBetween('+1 hour', '+1 week'),
            'metadata' => $this->faker->optional()->randomElements([
                'session_id' => $this->faker->uuid(),
                'browser' => $this->faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
                'os' => $this->faker->randomElement(['Windows', 'MacOS', 'Linux', 'Android', 'iOS']),
                'risk_score' => $this->faker->numberBetween(0, 100),
            ], $this->faker->numberBetween(0, 3)),
        ];
    }

    public function login(): self
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => SecurityEventType::Login,
            'security_level' => SecurityLevel::Low,
            'description' => 'User login successful',
        ]);
    }

    public function failedLogin(): self
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => SecurityEventType::FailedLogin,
            'security_level' => SecurityLevel::Medium,
            'description' => 'Failed login attempt',
        ]);
    }

    public function fraudAlert(): self
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => SecurityEventType::FraudAlert,
            'security_level' => SecurityLevel::Critical,
            'description' => 'Potential fraud detected',
            'blocked' => true,
        ]);
    }

    public function suspiciousActivity(): self
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => SecurityEventType::SuspiciousActivity,
            'security_level' => SecurityLevel::High,
            'description' => 'Suspicious activity pattern detected',
        ]);
    }

    public function largeTransaction(): self
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => SecurityEventType::LargeTransaction,
            'security_level' => SecurityLevel::High,
            'description' => 'Large transaction amount detected',
            'details' => ['transaction_amount' => $this->faker->randomFloat(2, 10000, 100000)],
        ]);
    }

    public function unresolved(): self
    {
        return $this->state(fn (array $attributes) => [
            'is_resolved' => false,
            'resolved_at' => null,
            'resolved_by' => null,
            'resolution_notes' => null,
        ]);
    }

    public function blocked(): self
    {
        return $this->state(fn (array $attributes) => [
            'blocked' => true,
            'blocked_until' => $this->faker->dateTimeBetween('+1 hour', '+1 week'),
        ]);
    }

    private function determineSecurityLevel(SecurityEventType $eventType): SecurityLevel
    {
        return match ($eventType) {
            SecurityEventType::Login, SecurityEventType::Logout => SecurityLevel::Low,
            SecurityEventType::FailedLogin, SecurityEventType::PasswordChange, SecurityEventType::PasswordReset => SecurityLevel::Medium,
            SecurityEventType::Transaction, SecurityEventType::UnusualLocation, SecurityEventType::CardBlock => SecurityLevel::High,
            SecurityEventType::LargeTransaction, SecurityEventType::AccountFreeze, SecurityEventType::SuspiciousActivity => SecurityLevel::High,
            SecurityEventType::FraudAlert => SecurityLevel::Critical,
            default => SecurityLevel::Medium,
        };
    }

    private function generateDescription(SecurityEventType $eventType): string
    {
        return match ($eventType) {
            SecurityEventType::Login => 'User login successful',
            SecurityEventType::Logout => 'User logout',
            SecurityEventType::FailedLogin => 'Failed login attempt',
            SecurityEventType::PasswordChange => 'Password changed by user',
            SecurityEventType::PasswordReset => 'Password reset requested',
            SecurityEventType::Transaction => 'Transaction performed',
            SecurityEventType::LargeTransaction => 'Large transaction amount detected',
            SecurityEventType::UnusualLocation => 'Login from unusual location',
            SecurityEventType::CardBlock => 'Card blocked due to security concerns',
            SecurityEventType::AccountFreeze => 'Account frozen',
            SecurityEventType::FraudAlert => 'Potential fraud detected',
            SecurityEventType::SuspiciousActivity => 'Suspicious activity pattern detected',
            SecurityEventType::DataAccess => 'Sensitive data accessed',
            SecurityEventType::PermissionChange => 'User permissions changed',
            SecurityEventType::ApiAccess => 'API access logged',
            SecurityEventType::ConfigurationChange => 'System configuration changed',
        };
    }
}

<?php

namespace App\Modules\Security\Enums;

enum SecurityLevel: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Medium => 'Medium',
            self::High => 'High',
            self::Critical => 'Critical',
        };
    }

    public function priority(): int
    {
        return match ($this) {
            self::Low => 1,
            self::Medium => 2,
            self::High => 3,
            self::Critical => 4,
        };
    }

    public function requiresImmediateNotification(): bool
    {
        return $this === self::Critical;
    }

    public function autoBlockThreshold(): bool
    {
        return in_array($this, [self::High, self::Critical]);
    }
}

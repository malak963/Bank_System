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
            self::Low => __('Low'),
            self::Medium => __('Medium'),
            self::High => __('High'),
            self::Critical => __('Critical'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Low => 'bg-slate-50 text-slate-700 ring-slate-200',
            self::Medium => 'bg-blue-50 text-blue-700 ring-blue-200',
            self::High => 'bg-amber-50 text-amber-700 ring-amber-200',
            self::Critical => 'bg-rose-50 text-rose-700 ring-rose-200',
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

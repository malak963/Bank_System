<?php

namespace App\Modules\CustomerService\Enums;

enum TicketPriority: string
{
    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
    case Urgent = 'urgent';
    case Critical = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::Low => __('Low'),
            self::Normal => __('Normal'),
            self::High => __('High'),
            self::Urgent => __('Urgent'),
            self::Critical => __('Critical'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Low => 'bg-slate-50 text-slate-700 ring-slate-200',
            self::Normal => 'bg-blue-50 text-blue-700 ring-blue-200',
            self::High => 'bg-amber-50 text-amber-700 ring-amber-200',
            self::Urgent => 'bg-orange-50 text-orange-700 ring-orange-200',
            self::Critical => 'bg-rose-50 text-rose-700 ring-rose-200',
        };
    }

    public function priority(): int
    {
        return match ($this) {
            self::Low => 1,
            self::Normal => 2,
            self::High => 3,
            self::Urgent => 4,
            self::Critical => 5,
        };
    }

    public function responseTimeHours(): int
    {
        return match ($this) {
            self::Low => 72,
            self::Normal => 48,
            self::High => 24,
            self::Urgent => 8,
            self::Critical => 4,
        };
    }

    public function requiresEscalation(): bool
    {
        return in_array($this, [self::Urgent, self::Critical]);
    }
}

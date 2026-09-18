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
            self::Low => 'Low',
            self::Normal => 'Normal',
            self::High => 'High',
            self::Urgent => 'Urgent',
            self::Critical => 'Critical',
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

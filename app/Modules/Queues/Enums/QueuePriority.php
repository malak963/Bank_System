<?php

namespace App\Modules\Queues\Enums;

enum QueuePriority: string
{
    case Low = 'low';
    case Normal = 'normal';
    case High = 'high';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::Low => __('Low'),
            self::Normal => __('Normal'),
            self::High => __('High'),
            self::Urgent => __('Urgent'),
        };
    }

    public function weight(): int
    {
        return match ($this) {
            self::Low => 1,
            self::Normal => 2,
            self::High => 3,
            self::Urgent => 4,
        };
    }
}

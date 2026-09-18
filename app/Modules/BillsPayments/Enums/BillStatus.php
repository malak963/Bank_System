<?php

namespace App\Modules\BillsPayments\Enums;

enum BillStatus: string
{
    case PENDING = 'pending';
    case SCHEDULED = 'scheduled';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function getLabel(): string
    {
        return match($this) {
            self::PENDING => __('Pending'),
            self::SCHEDULED => __('Scheduled'),
            self::PROCESSING => __('Processing'),
            self::COMPLETED => __('Completed'),
            self::FAILED => __('Failed'),
            self::CANCELLED => __('Cancelled'),
            self::REFUNDED => __('Refunded'),
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::SCHEDULED => 'blue',
            self::PROCESSING => 'purple',
            self::COMPLETED => 'emerald',
            self::FAILED => 'red',
            self::CANCELLED => 'gray',
            self::REFUNDED => 'orange',
        };
    }
}

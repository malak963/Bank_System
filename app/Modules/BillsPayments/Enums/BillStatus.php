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
            self::PENDING => 'Pending',
            self::SCHEDULED => 'Scheduled',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
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

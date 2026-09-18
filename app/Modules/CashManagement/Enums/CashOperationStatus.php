<?php

namespace App\Modules\CashManagement\Enums;

enum CashOperationStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Approved => __('Approved'),
            self::Rejected => __('Rejected'),
            self::Completed => __('Completed'),
            self::Cancelled => __('Cancelled'),
        };
    }
}
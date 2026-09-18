<?php

namespace App\Modules\Transfers\Enums;

enum TransferStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case OnHold = 'on_hold';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Processing => __('Processing'),
            self::Completed => __('Completed'),
            self::Failed => __('Failed'),
            self::Cancelled => __('Cancelled'),
            self::OnHold => __('On Hold'),
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Failed,
            self::Cancelled,
        ]);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [
            self::Pending,
            self::Processing,
        ]);
    }

    public function canBeRetried(): bool
    {
        return $this === self::Failed;
    }
}

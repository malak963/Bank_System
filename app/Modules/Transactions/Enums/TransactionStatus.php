<?php

namespace App\Modules\Transactions\Enums;

enum TransactionStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
    case Reversed = 'reversed';
    case Cancelled = 'cancelled';
    case OnHold = 'on_hold';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Processing => __('Processing'),
            self::Completed => __('Completed'),
            self::Failed => __('Failed'),
            self::Reversed => __('Reversed'),
            self::Cancelled => __('Cancelled'),
            self::OnHold => __('On Hold'),
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Failed,
            self::Reversed,
            self::Cancelled,
        ]);
    }

    public function canBeReversed(): bool
    {
        return $this === self::Completed;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this, [
            self::Pending,
            self::Processing,
        ]);
    }
}

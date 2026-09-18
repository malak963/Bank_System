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
            self::Pending => 'Pending',
            self::Processing => 'Processing',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
            self::Reversed => 'Reversed',
            self::Cancelled => 'Cancelled',
            self::OnHold => 'On Hold',
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

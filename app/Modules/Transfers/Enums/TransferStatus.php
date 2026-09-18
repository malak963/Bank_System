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
            self::Pending => 'Pending',
            self::Processing => 'Processing',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
            self::Cancelled => 'Cancelled',
            self::OnHold => 'On Hold',
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

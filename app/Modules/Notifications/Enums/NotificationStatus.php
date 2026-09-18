<?php

namespace App\Modules\Notifications\Enums;

enum NotificationStatus: string
{
    case Pending = 'pending';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Failed = 'failed';
    case Read = 'read';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Sent => __('Sent'),
            self::Delivered => __('Delivered'),
            self::Failed => __('Failed'),
            self::Read => __('Read'),
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [
            self::Delivered,
            self::Failed,
            self::Read,
        ]);
    }

    public function canBeRetried(): bool
    {
        return $this === self::Failed;
    }
}

<?php

namespace App\Modules\Notifications\Enums;

enum NotificationChannel: string
{
    case Email = 'email';
    case SMS = 'sms';
    case Push = 'push';
    case InApp = 'in_app';
    case WhatsApp = 'whatsapp';

    public function label(): string
    {
        return match ($this) {
            self::Email => __('Email'),
            self::SMS => __('SMS'),
            self::Push => __('Push Notification'),
            self::InApp => __('In-App'),
            self::WhatsApp => __('WhatsApp'),
        };
    }

    public function requiresDeliveryConfirmation(): bool
    {
        return in_array($this, [
            self::SMS,
            self::Push,
        ]);
    }

    public function canHaveAttachments(): bool
    {
        return in_array($this, [
            self::Email,
            self::WhatsApp,
        ]);
    }
}

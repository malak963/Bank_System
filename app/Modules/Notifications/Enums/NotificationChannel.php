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
            self::Email => 'Email',
            self::SMS => 'SMS',
            self::Push => 'Push Notification',
            self::InApp => 'In-App',
            self::WhatsApp => 'WhatsApp',
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

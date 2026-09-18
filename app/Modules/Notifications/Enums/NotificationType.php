<?php

namespace App\Modules\Notifications\Enums;

enum NotificationType: string
{
    case Transaction = 'transaction';
    case Account = 'account';
    case Card = 'card';
    case Loan = 'loan';
    case Security = 'security';
    case Marketing = 'marketing';
    case System = 'system';
    case Appointment = 'appointment';
    case Payment = 'payment';
    case Balance = 'balance';

    public function label(): string
    {
        return match ($this) {
            self::Transaction => __('Transaction'),
            self::Account => __('Account'),
            self::Card => __('Card'),
            self::Loan => __('Loan'),
            self::Security => __('Security'),
            self::Marketing => __('Marketing'),
            self::System => __('System'),
            self::Appointment => __('Appointment'),
            self::Payment => __('Payment'),
            self::Balance => __('Balance'),
        };
    }

    public function isUrgent(): bool
    {
        return in_array($this, [
            self::Security,
            self::Card,
        ]);
    }

    public function requiresImmediateAction(): bool
    {
        return in_array($this, [
            self::Security,
            self::Card,
        ]);
    }
}

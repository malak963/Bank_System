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
            self::Transaction => 'Transaction',
            self::Account => 'Account',
            self::Card => 'Card',
            self::Loan => 'Loan',
            self::Security => 'Security',
            self::Marketing => 'Marketing',
            self::System => 'System',
            self::Appointment => 'Appointment',
            self::Payment => 'Payment',
            self::Balance => 'Balance',
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

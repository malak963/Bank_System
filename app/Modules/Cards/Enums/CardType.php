<?php

namespace App\Modules\Cards\Enums;

enum CardType: string
{
    case Debit = 'debit';
    case Credit = 'credit';
    case Prepaid = 'prepaid';
    case Virtual = 'virtual';

    public function label(): string
    {
        return match ($this) {
            self::Debit => __('Debit Card'),
            self::Credit => __('Credit Card'),
            self::Prepaid => __('Prepaid Card'),
            self::Virtual => __('Virtual Card'),
        };
    }

    public function requiresCreditCheck(): bool
    {
        return $this === self::Credit;
    }

    public function hasDailyLimit(): bool
    {
        return in_array($this, [self::Debit, self::Prepaid]);
    }

    public function canBeInternational(): bool
    {
        return in_array($this, [self::Credit, self::Debit]);
    }
}

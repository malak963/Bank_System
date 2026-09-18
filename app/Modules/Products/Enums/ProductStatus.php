<?php

namespace App\Modules\Products\Enums;

enum ProductStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Matured = 'matured';
    case Closed = 'closed';
    case Suspended = 'suspended';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::Active => __('Active'),
            self::Inactive => __('Inactive'),
            self::Matured => __('Matured'),
            self::Closed => __('Closed'),
            self::Suspended => __('Suspended'),
            self::Pending => __('Pending'),
        };
    }

    public function canHaveTransactions(): bool
    {
        return in_array($this, [
            self::Active,
            self::Suspended,
        ]);
    }

    public function canBeClosed(): bool
    {
        return in_array($this, [
            self::Active,
            self::Matured,
            self::Suspended,
        ]);
    }

    public function isFinal(): bool
    {
        return in_array($this, [
            self::Matured,
            self::Closed,
        ]);
    }
}

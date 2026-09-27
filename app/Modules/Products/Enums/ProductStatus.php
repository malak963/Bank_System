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

    public function badgeColor(): string
    {
        return match ($this) {
            self::Active => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::Inactive => 'bg-slate-50 text-slate-700 ring-slate-200',
            self::Matured => 'bg-blue-50 text-blue-700 ring-blue-200',
            self::Closed => 'bg-slate-50 text-slate-700 ring-slate-200',
            self::Suspended => 'bg-rose-50 text-rose-700 ring-rose-200',
            self::Pending => 'bg-amber-50 text-amber-700 ring-amber-200',
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

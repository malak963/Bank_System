<?php

namespace App\Modules\Cards\Enums;

enum CardStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Blocked = 'blocked';
    case Expired = 'expired';
    case Lost = 'lost';
    case Stolen = 'stolen';
    case Damaged = 'damaged';
    case Pending = 'pending';
    case Replaced = 'replaced';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Inactive => 'Inactive',
            self::Blocked => 'Blocked',
            self::Expired => 'Expired',
            self::Lost => 'Lost',
            self::Stolen => 'Stolen',
            self::Damaged => 'Damaged',
            self::Pending => 'Pending',
            self::Replaced => 'Replaced',
        };
    }

    public function canPerformTransactions(): bool
    {
        return $this === self::Active;
    }

    public function isBlocked(): bool
    {
        return in_array($this, [self::Blocked, self::Lost, self::Stolen, self::Damaged]);
    }

    public function canBeUnblocked(): bool
    {
        return in_array($this, [self::Blocked, self::Inactive]);
    }

    public function requiresReplacement(): bool
    {
        return in_array($this, [self::Lost, self::Stolen, self::Damaged, self::Expired]);
    }
}

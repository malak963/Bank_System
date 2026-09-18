<?php

namespace App\Modules\Cards\Enums;

enum CardBrand: string
{
    case Visa = 'visa';
    case Mastercard = 'mastercard';
    case AmericanExpress = 'american_express';
    case Discover = 'discover';
    case Maestro = 'maestro';
    case UnionPay = 'union_pay';

    public function label(): string
    {
        return match ($this) {
            self::Visa => 'Visa',
            self::Mastercard => 'Mastercard',
            self::AmericanExpress => 'American Express',
            self::Discover => 'Discover',
            self::Maestro => 'Maestro',
            self::UnionPay => 'UnionPay',
        };
    }

    public function cardNumberLength(): int
    {
        return match ($this) {
            self::Visa, self::Mastercard, self::Discover => 16,
            self::AmericanExpress => 15,
            self::Maestro => 16,
            self::UnionPay => 16,
        };
    }

    public function cvvLength(): int
    {
        return match ($this) {
            self::AmericanExpress => 4,
            default => 3,
        };
    }

    public function startsWith(): array
    {
        return match ($this) {
            self::Visa => ['4'],
            self::Mastercard => ['51', '52', '53', '54', '55'],
            self::AmericanExpress => ['34', '37'],
            self::Discover => ['6011', '644', '65'],
            self::Maestro => ['5018', '5020', '5038', '6304'],
            self::UnionPay => ['62'],
        };
    }
}

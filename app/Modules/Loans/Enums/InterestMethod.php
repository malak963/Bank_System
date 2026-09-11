<?php

namespace App\Modules\Loans\Enums;

enum InterestMethod: string
{
    case ReducingBalance = 'reducing_balance';
    case FlatRate = 'flat_rate';

    public function label(): string
    {
        return match ($this) {
            self::ReducingBalance => 'Reducing Balance',
            self::FlatRate => 'Flat Rate',
        };
    }
}

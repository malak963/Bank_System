<?php

namespace App\Modules\Loans\Enums;

enum InterestMethod: string
{
    case ReducingBalance = 'reducing_balance';
    case FlatRate = 'flat_rate';

    public function label(): string
    {
        return match ($this) {
            self::ReducingBalance => __('Reducing Balance'),
            self::FlatRate => __('Flat Rate'),
        };
    }
}

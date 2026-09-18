<?php

namespace App\Modules\Loans\Enums;

enum RepaymentFrequency: string
{
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';

    public function monthsPerPeriod(): int
    {
        return match ($this) {
            self::Monthly => 1,
            self::Quarterly => 3,
        };
    }

    public function periodsPerYear(): int
    {
        return match ($this) {
            self::Monthly => 12,
            self::Quarterly => 4,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Monthly => __('Monthly'),
            self::Quarterly => __('Quarterly'),
        };
    }
}

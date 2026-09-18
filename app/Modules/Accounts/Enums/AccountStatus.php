<?php

namespace App\Modules\Accounts\Enums;

enum AccountStatus: string
{
    case Open = 'open';
    case Frozen = 'frozen';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => __('Open'),
            self::Frozen => __('Frozen'),
            self::Closed => __('Closed'),
        };
    }
}

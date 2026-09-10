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
            self::Open => 'Open',
            self::Frozen => 'Frozen',
            self::Closed => 'Closed',
        };
    }
}

<?php

namespace App\Modules\Customers\Enums;

enum CustomerStatus: string
{
    case Prospect = 'prospect';
    case Active = 'active';
    case Suspended = 'suspended';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Prospect => 'Prospect',
            self::Active => 'Active',
            self::Suspended => 'Suspended',
            self::Closed => 'Closed',
        };
    }
}

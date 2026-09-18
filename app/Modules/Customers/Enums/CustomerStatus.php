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
            self::Prospect => __('Prospect'),
            self::Active => __('Active'),
            self::Suspended => __('Suspended'),
            self::Closed => __('Closed'),
        };
    }
}

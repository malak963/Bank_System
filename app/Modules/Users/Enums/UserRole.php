<?php

namespace App\Modules\Users\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Employee = 'employee';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Manager => 'Manager',
            self::Employee => 'Employee',
            self::Customer => 'Customer',
        };
    }

    public function canManageUsers(): bool
    {
        return in_array($this, [self::Admin, self::Manager], true);
    }
}

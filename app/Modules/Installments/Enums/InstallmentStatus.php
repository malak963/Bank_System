<?php

namespace App\Modules\Installments\Enums;

enum InstallmentStatus: string
{
    case Pending = 'pending';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Overdue = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::PartiallyPaid => __('Partially Paid'),
            self::Paid => __('Paid'),
            self::Overdue => __('Overdue'),
        };
    }
}

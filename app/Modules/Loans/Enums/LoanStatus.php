<?php

namespace App\Modules\Loans\Enums;

enum LoanStatus: string
{
    case Pending = 'pending';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Disbursed = 'disbursed';
    case Active = 'active';
    case PaidOff = 'paid_off';
    case Defaulted = 'defaulted';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::UnderReview => 'Under Review',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Disbursed => 'Disbursed',
            self::Active => 'Active',
            self::PaidOff => 'Paid Off',
            self::Defaulted => 'Defaulted',
            self::Cancelled => 'Cancelled',
        };
    }
}

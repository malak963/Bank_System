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
            self::Pending => __('Pending'),
            self::UnderReview => __('Under Review'),
            self::Approved => __('Approved'),
            self::Rejected => __('Rejected'),
            self::Disbursed => __('Disbursed'),
            self::Active => __('Active'),
            self::PaidOff => __('Paid Off'),
            self::Defaulted => __('Defaulted'),
            self::Cancelled => __('Cancelled'),
        };
    }
}

<?php

namespace App\Modules\Appointments\Enums;

enum ServiceType: string
{
    case General = 'general';
    case LoanConsultation = 'loan_consultation';
    case AccountOpening = 'account_opening';
    case WealthManagement = 'wealth_management';
    case CardServices = 'card_services';
    case ComplaintResolution = 'complaint_resolution';

    public function label(): string
    {
        return match ($this) {
            self::General => __('General Banking'),
            self::LoanConsultation => __('Loan Consultation'),
            self::AccountOpening => __('Account Opening'),
            self::WealthManagement => __('Wealth Management'),
            self::CardServices => __('Card Services'),
            self::ComplaintResolution => __('Complaint Resolution'),
        };
    }

    public function estimatedDuration(): int // minutes
    {
        return match ($this) {
            self::General => 15,
            self::LoanConsultation => 30,
            self::AccountOpening => 20,
            self::WealthManagement => 45,
            self::CardServices => 15,
            self::ComplaintResolution => 25,
        };
    }
}

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
            self::General => 'General Banking',
            self::LoanConsultation => 'Loan Consultation',
            self::AccountOpening => 'Account Opening',
            self::WealthManagement => 'Wealth Management',
            self::CardServices => 'Card Services',
            self::ComplaintResolution => 'Complaint Resolution',
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

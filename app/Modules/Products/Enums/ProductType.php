<?php

namespace App\Modules\Products\Enums;

enum ProductType: string
{
    case SavingsAccount = 'savings_account';
    case FixedDeposit = 'fixed_deposit';
    case InvestmentAccount = 'investment_account';
    case CertificateOfDeposit = 'certificate_of_deposit';
    case MutualFund = 'mutual_fund';
    case RetirementAccount = 'retirement_account';
    case EducationAccount = 'education_account';
    case HealthAccount = 'health_account';

    public function label(): string
    {
        return match ($this) {
            self::SavingsAccount => __('Savings Account'),
            self::FixedDeposit => __('Fixed Deposit'),
            self::InvestmentAccount => __('Investment Account'),
            self::CertificateOfDeposit => __('Certificate of Deposit'),
            self::MutualFund => __('Mutual Fund'),
            self::RetirementAccount => __('Retirement Account'),
            self::EducationAccount => __('Education Account'),
            self::HealthAccount => __('Health Account'),
        };
    }

    public function isInvestment(): bool
    {
        return in_array($this, [
            self::InvestmentAccount,
            self::MutualFund,
        ]);
    }

    public function isSavings(): bool
    {
        return in_array($this, [
            self::SavingsAccount,
            self::FixedDeposit,
            self::CertificateOfDeposit,
        ]);
    }

    public function requiresRiskDisclosure(): bool
    {
        return $this->isInvestment();
    }

    public function minBalance(): float
    {
        return match ($this) {
            self::SavingsAccount => 100,
            self::FixedDeposit => 1000,
            self::InvestmentAccount => 5000,
            self::CertificateOfDeposit => 1000,
            self::MutualFund => 1000,
            self::RetirementAccount => 500,
            self::EducationAccount => 500,
            self::HealthAccount => 500,
        };
    }
}

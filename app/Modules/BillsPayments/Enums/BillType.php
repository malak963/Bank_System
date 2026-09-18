<?php

namespace App\Modules\BillsPayments\Enums;

enum BillType: string
{
    case ELECTRICITY = 'electricity';
    case WATER = 'water';
    case GAS = 'gas';
    case INTERNET = 'internet';
    case PHONE = 'phone';
    case TELEVISION = 'television';
    case INSURANCE = 'insurance';
    case TAX = 'tax';
    case LOAN_INSTALLMENT = 'loan_installment';
    case SUBSCRIPTION = 'subscription';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match($this) {
            self::ELECTRICITY => 'Electricity',
            self::WATER => 'Water',
            self::GAS => 'Gas',
            self::INTERNET => 'Internet',
            self::PHONE => 'Phone',
            self::TELEVISION => 'Television',
            self::INSURANCE => 'Insurance',
            self::TAX => 'Tax',
            self::LOAN_INSTALLMENT => 'Loan Installment',
            self::SUBSCRIPTION => 'Subscription',
            self::OTHER => 'Other',
        };
    }
}

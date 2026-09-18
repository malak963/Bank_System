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
            self::ELECTRICITY => __('Electricity'),
            self::WATER => __('Water'),
            self::GAS => __('Gas'),
            self::INTERNET => __('Internet'),
            self::PHONE => __('Phone'),
            self::TELEVISION => __('Television'),
            self::INSURANCE => __('Insurance'),
            self::TAX => __('Tax'),
            self::LOAN_INSTALLMENT => __('Loan Installment'),
            self::SUBSCRIPTION => __('Subscription'),
            self::OTHER => __('Other'),
        };
    }
}

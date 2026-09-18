<?php

namespace App\Modules\Loans\Enums;

enum LoanPaymentMethod: string
{
    case AccountDebit = 'account_debit';
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';

    public function label(): string
    {
        return match ($this) {
            self::AccountDebit => __('Account Debit'),
            self::Cash => __('Cash'),
            self::BankTransfer => __('Bank Transfer'),
        };
    }
}

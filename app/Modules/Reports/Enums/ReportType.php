<?php

namespace App\Modules\Reports\Enums;

enum ReportType: string
{
    case Transaction = 'transaction';
    case Account = 'account';
    case Customer = 'customer';
    case Loan = 'loan';
    case Card = 'card';
    case Branch = 'branch';
    case Revenue = 'revenue';
    case Compliance = 'compliance';
    case Audit = 'audit';
    case Performance = 'performance';

    public function label(): string
    {
        return match ($this) {
            self::Transaction => __('Transaction Report'),
            self::Account => __('Account Report'),
            self::Customer => __('Customer Report'),
            self::Loan => __('Loan Report'),
            self::Card => __('Card Report'),
            self::Branch => __('Branch Report'),
            self::Revenue => __('Revenue Report'),
            self::Compliance => __('Compliance Report'),
            self::Audit => __('Audit Report'),
            self::Performance => __('Performance Report'),
        };
    }

    public function isFinancial(): bool
    {
        return in_array($this, [
            self::Transaction,
            self::Account,
            self::Loan,
            self::Revenue,
        ]);
    }

    public function isOperational(): bool
    {
        return in_array($this, [
            self::Branch,
            self::Performance,
        ]);
    }

    public function isRegulatory(): bool
    {
        return in_array($this, [
            self::Compliance,
            self::Audit,
        ]);
    }
}

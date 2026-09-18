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
            self::Transaction => 'Transaction Report',
            self::Account => 'Account Report',
            self::Customer => 'Customer Report',
            self::Loan => 'Loan Report',
            self::Card => 'Card Report',
            self::Branch => 'Branch Report',
            self::Revenue => 'Revenue Report',
            self::Compliance => 'Compliance Report',
            self::Audit => 'Audit Report',
            self::Performance => 'Performance Report',
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

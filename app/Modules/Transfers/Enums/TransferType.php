<?php

namespace App\Modules\Transfers\Enums;

enum TransferType: string
{
    case Internal = 'internal';
    case External = 'external';
    case International = 'international';
    case StandingOrder = 'standing_order';
    case DirectDebit = 'direct_debit';

    public function label(): string
    {
        return match ($this) {
            self::Internal => 'Internal Transfer',
            self::External => 'External Transfer',
            self::International => 'International Transfer',
            self::StandingOrder => 'Standing Order',
            self::DirectDebit => 'Direct Debit',
        };
    }

    public function requiresBeneficiaryBank(): bool
    {
        return in_array($this, [
            self::External,
            self::International,
        ]);
    }

    public function requiresSwiftCode(): bool
    {
        return $this === self::International;
    }

    public function isScheduled(): bool
    {
        return in_array($this, [
            self::StandingOrder,
            self::DirectDebit,
        ]);
    }

    public function processingTimeHours(): int
    {
        return match ($this) {
            self::Internal => 1,
            self::External => 24,
            self::International => 72,
            self::StandingOrder => 24,
            self::DirectDebit => 24,
        };
    }
}

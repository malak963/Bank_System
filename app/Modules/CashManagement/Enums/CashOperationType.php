<?php

namespace App\Modules\CashManagement\Enums;

enum CashOperationType: string
{
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
    case Transfer = 'transfer';
    case Replenishment = 'replenishment';
    case WithdrawalToVault = 'withdrawal_to_vault';
    case DepositFromVault = 'deposit_from_vault';

    public function label(): string
    {
        return match ($this) {
            self::Deposit => 'Deposit',
            self::Withdrawal => 'Withdrawal',
            self::Transfer => 'Transfer',
            self::Replenishment => 'Replenishment',
            self::WithdrawalToVault => 'Withdrawal to Vault',
            self::DepositFromVault => 'Deposit from Vault',
        };
    }
}
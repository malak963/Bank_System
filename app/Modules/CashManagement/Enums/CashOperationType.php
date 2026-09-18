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
            self::Deposit => __('Deposit'),
            self::Withdrawal => __('Withdrawal'),
            self::Transfer => __('Transfer'),
            self::Replenishment => __('Replenishment'),
            self::WithdrawalToVault => __('Withdrawal to Vault'),
            self::DepositFromVault => __('Deposit from Vault'),
        };
    }
}
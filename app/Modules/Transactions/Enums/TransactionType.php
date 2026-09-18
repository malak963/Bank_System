<?php

namespace App\Modules\Transactions\Enums;

enum TransactionType: string
{
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
    case Transfer = 'transfer';
    case Fee = 'fee';
    case Interest = 'interest';
    case Penalty = 'penalty';
    case Reversal = 'reversal';
    case Refund = 'refund';
    case BillPayment = 'bill_payment';
    case CardPayment = 'card_payment';
    case AtmWithdrawal = 'atm_withdrawal';
    case ChequeDeposit = 'cheque_deposit';
    case ChequeWithdrawal = 'cheque_withdrawal';
    case OnlineTransfer = 'online_transfer';
    case StandingOrder = 'standing_order';
    case DirectDebit = 'direct_debit';

    public function label(): string
    {
        return match ($this) {
            self::Deposit => 'Deposit',
            self::Withdrawal => 'Withdrawal',
            self::Transfer => 'Transfer',
            self::Fee => 'Fee',
            self::Interest => 'Interest',
            self::Penalty => 'Penalty',
            self::Reversal => 'Reversal',
            self::Refund => 'Refund',
            self::BillPayment => 'Bill Payment',
            self::CardPayment => 'Card Payment',
            self::AtmWithdrawal => 'ATM Withdrawal',
            self::ChequeDeposit => 'Cheque Deposit',
            self::ChequeWithdrawal => 'Cheque Withdrawal',
            self::OnlineTransfer => 'Online Transfer',
            self::StandingOrder => 'Standing Order',
            self::DirectDebit => 'Direct Debit',
        };
    }

    public function isCredit(): bool
    {
        return in_array($this, [
            self::Deposit,
            self::Interest,
            self::Refund,
        ]);
    }

    public function isDebit(): bool
    {
        return in_array($this, [
            self::Withdrawal,
            self::Transfer,
            self::Fee,
            self::Penalty,
            self::BillPayment,
            self::CardPayment,
            self::AtmWithdrawal,
            self::ChequeWithdrawal,
            self::OnlineTransfer,
            self::StandingOrder,
            self::DirectDebit,
        ]);
    }

    public function isReversible(): bool
    {
        return in_array($this, [
            self::Deposit,
            self::Withdrawal,
            self::Transfer,
            self::CardPayment,
            self::AtmWithdrawal,
            self::OnlineTransfer,
        ]);
    }
}

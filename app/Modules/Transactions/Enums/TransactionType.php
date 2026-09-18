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
            self::Deposit => __('Deposit'),
            self::Withdrawal => __('Withdrawal'),
            self::Transfer => __('Transfer'),
            self::Fee => __('Fee'),
            self::Interest => __('Interest'),
            self::Penalty => __('Penalty'),
            self::Reversal => __('Reversal'),
            self::Refund => __('Refund'),
            self::BillPayment => __('Bill Payment'),
            self::CardPayment => __('Card Payment'),
            self::AtmWithdrawal => __('ATM Withdrawal'),
            self::ChequeDeposit => __('Cheque Deposit'),
            self::ChequeWithdrawal => __('Cheque Withdrawal'),
            self::OnlineTransfer => __('Online Transfer'),
            self::StandingOrder => __('Standing Order'),
            self::DirectDebit => __('Direct Debit'),
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

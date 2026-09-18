<?php

namespace App\Modules\CustomerService\Enums;

enum TicketCategory: string
{
    case Account = 'account';
    case Transaction = 'transaction';
    case Card = 'card';
    case Loan = 'loan';
    case Technical = 'technical';
    case Billing = 'billing';
    case Fraud = 'fraud';
    case General = 'general';
    case Complaint = 'complaint';
    case Feedback = 'feedback';
    case FeatureRequest = 'feature_request';

    public function label(): string
    {
        return match ($this) {
            self::Account => __('Account Issues'),
            self::Transaction => __('Transaction Issues'),
            self::Card => __('Card Issues'),
            self::Loan => __('Loan Issues'),
            self::Technical => __('Technical Support'),
            self::Billing => __('Billing Issues'),
            self::Fraud => __('Fraud Report'),
            self::General => __('General Inquiry'),
            self::Complaint => __('Complaint'),
            self::Feedback => __('Feedback'),
            self::FeatureRequest => __('Feature Request'),
        };
    }

    public function requiresEscalation(): bool
    {
        return in_array($this, [
            self::Fraud,
            self::Complaint,
        ]);
    }

    public function defaultPriority(): TicketPriority
    {
        return match ($this) {
            self::Fraud => TicketPriority::Critical,
            self::Complaint => TicketPriority::High,
            self::Card, self::Transaction => TicketPriority::Normal,
            self::Account, self::Loan => TicketPriority::Normal,
            self::Technical => TicketPriority::Normal,
            self::Billing => TicketPriority::High,
            self::General => TicketPriority::Low,
            self::Feedback => TicketPriority::Low,
            self::FeatureRequest => TicketPriority::Low,
        };
    }
}

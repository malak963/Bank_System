<?php

namespace App\Modules\CustomerService\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case PendingCustomer = 'pending_customer';
    case Resolved = 'resolved';
    case Closed = 'closed';
    case Escalated = 'escalated';

    public function label(): string
    {
        return match ($this) {
            self::Open => __('Open'),
            self::InProgress => __('In Progress'),
            self::PendingCustomer => __('Pending Customer'),
            self::Resolved => __('Resolved'),
            self::Closed => __('Closed'),
            self::Escalated => __('Escalated'),
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [
            self::Open,
            self::InProgress,
            self::PendingCustomer,
            self::Escalated,
        ]);
    }

    public function isFinal(): bool
    {
        return in_array($this, [
            self::Resolved,
            self::Closed,
        ]);
    }

    public function canBeReopened(): bool
    {
        return $this === self::Closed;
    }
}

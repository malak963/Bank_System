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
            self::Open => 'Open',
            self::InProgress => 'In Progress',
            self::PendingCustomer => 'Pending Customer',
            self::Resolved => 'Resolved',
            self::Closed => 'Closed',
            self::Escalated => 'Escalated',
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

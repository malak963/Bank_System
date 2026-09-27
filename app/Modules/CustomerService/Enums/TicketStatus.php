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

    public function badgeColor(): string
    {
        return match ($this) {
            self::Open => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::InProgress => 'bg-blue-50 text-blue-700 ring-blue-200',
            self::PendingCustomer => 'bg-amber-50 text-amber-700 ring-amber-200',
            self::Resolved => 'bg-teal-50 text-teal-700 ring-teal-200',
            self::Closed => 'bg-slate-50 text-slate-700 ring-slate-200',
            self::Escalated => 'bg-rose-50 text-rose-700 ring-rose-200',
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

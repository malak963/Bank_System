<?php

namespace App\Modules\Reports\Enums;

enum ReportStatus: string
{
    case Pending = 'pending';
    case Generating = 'generating';
    case Completed = 'completed';
    case Failed = 'failed';
    case Scheduled = 'scheduled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Generating => __('Generating'),
            self::Completed => __('Completed'),
            self::Failed => __('Failed'),
            self::Scheduled => __('Scheduled'),
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-50 text-amber-700 ring-amber-200',
            self::Generating => 'bg-blue-50 text-blue-700 ring-blue-200',
            self::Completed => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
            self::Failed => 'bg-rose-50 text-rose-700 ring-rose-200',
            self::Scheduled => 'bg-purple-50 text-purple-700 ring-purple-200',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Failed,
        ]);
    }

    public function canBeRegenerated(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Failed,
        ]);
    }
}

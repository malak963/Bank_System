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

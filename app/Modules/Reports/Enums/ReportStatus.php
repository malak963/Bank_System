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
            self::Pending => 'Pending',
            self::Generating => 'Generating',
            self::Completed => 'Completed',
            self::Failed => 'Failed',
            self::Scheduled => 'Scheduled',
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

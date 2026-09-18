<?php

namespace App\Modules\Reports\Enums;

enum ReportFormat: string
{
    case PDF = 'pdf';
    case Excel = 'excel';
    case CSV = 'csv';
    case JSON = 'json';

    public function label(): string
    {
        return match ($this) {
            self::PDF => __('PDF'),
            self::Excel => __('Excel'),
            self::CSV => __('CSV'),
            self::JSON => __('JSON'),
        };
    }

    public function mimeType(): string
    {
        return match ($this) {
            self::PDF => 'application/pdf',
            self::Excel => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            self::CSV => 'text/csv',
            self::JSON => 'application/json',
        };
    }

    public function fileExtension(): string
    {
        return match ($this) {
            self::PDF => 'pdf',
            self::Excel => 'xlsx',
            self::CSV => 'csv',
            self::JSON => 'json',
        };
    }
}

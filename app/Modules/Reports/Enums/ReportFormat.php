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
            self::PDF => 'PDF',
            self::Excel => 'Excel',
            self::CSV => 'CSV',
            self::JSON => 'JSON',
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

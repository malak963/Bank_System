<?php

namespace App\Modules\Reports\Models;

use App\Modules\Branches\Models\Branch;
use App\Modules\Reports\Enums\ReportFormat;
use App\Modules\Reports\Enums\ReportStatus;
use App\Modules\Reports\Enums\ReportType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';

    protected $fillable = [
        'report_type',
        'title',
        'description',
        'status',
        'format',
        'parameters',
        'generated_by',
        'branch_id',
        'file_path',
        'file_size',
        'record_count',
        'generated_at',
        'expires_at',
        'scheduled_at',
        'error_message',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'report_type' => ReportType::class,
            'status' => ReportStatus::class,
            'format' => ReportFormat::class,
            'parameters' => 'array',
            'metadata' => 'array',
            'generated_at' => 'datetime',
            'expires_at' => 'datetime',
            'scheduled_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'generated_by');
    }

    public function isCompleted(): bool
    {
        return $this->status === ReportStatus::Completed;
    }

    public function isFailed(): bool
    {
        return $this->status === ReportStatus::Failed;
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canBeDownloaded(): bool
    {
        return $this->isCompleted() && !$this->isExpired() && $this->file_path;
    }

    public function canBeRegenerated(): bool
    {
        return $this->status->canBeRegenerated();
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', ReportStatus::Completed);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', ReportStatus::Failed);
    }

    public function scopePending($query)
    {
        return $query->where('status', ReportStatus::Pending);
    }

    public function scopeByType($query, mixed $type)
    {
        $reportType = is_string($type) ? ReportType::from($type) : $type;
        return $query->where('report_type', $reportType);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->whereBetween('expires_at', [
            now(),
            now()->addDays($days)
        ]);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', ReportStatus::Scheduled)
            ->where('scheduled_at', '>', now());
    }

    protected static function newFactory(): \App\Modules\Reports\Database\Factories\ReportFactory
    {
        return \App\Modules\Reports\Database\Factories\ReportFactory::new();
    }
}

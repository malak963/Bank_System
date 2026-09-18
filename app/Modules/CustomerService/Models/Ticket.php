<?php

namespace App\Modules\CustomerService\Models;

use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Models\Customer;
use App\Modules\CustomerService\Enums\TicketCategory;
use App\Modules\CustomerService\Enums\TicketPriority;
use App\Modules\CustomerService\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'ticket_number',
        'customer_id',
        'branch_id',
        'category',
        'priority',
        'status',
        'subject',
        'description',
        'assigned_to',
        'resolved_by',
        'resolution',
        'resolved_at',
        'closed_at',
        'first_response_at',
        'escalated_at',
        'escalated_to',
        'customer_satisfaction',
        'tags',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'category' => TicketCategory::class,
            'priority' => TicketPriority::class,
            'status' => TicketStatus::class,
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'first_response_at' => 'datetime',
            'escalated_at' => 'datetime',
            'customer_satisfaction' => 'integer',
            'tags' => 'array',
            'metadata' => 'array',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'resolved_by');
    }

    public function escalatedTo(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'escalated_to');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(TicketResponse::class);
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isOverdue(): bool
    {
        if ($this->first_response_at) {
            return false;
        }

        $responseTime = $this->priority->responseTimeHours();
        return $this->created_at->addHours($responseTime)->isPast();
    }

    public function assign(int $userId): void
    {
        $this->update([
            'assigned_to' => $userId,
            'status' => TicketStatus::InProgress,
        ]);
    }

    public function resolve(string $resolution, ?int $resolvedBy = null): void
    {
        $this->update([
            'status' => TicketStatus::Resolved,
            'resolution' => $resolution,
            'resolved_by' => $resolvedBy ?? auth()->id(),
            'resolved_at' => now(),
        ]);
    }

    public function close(?int $closedBy = null): void
    {
        $this->update([
            'status' => TicketStatus::Closed,
            'closed_at' => now(),
        ]);
    }

    public function reopen(): void
    {
        if (!$this->status->canBeReopened()) {
            throw new \Exception('This ticket cannot be reopened');
        }

        $this->update([
            'status' => TicketStatus::Open,
            'closed_at' => null,
        ]);
    }

    public function escalate(int $escalatedTo, string $reason): void
    {
        $this->update([
            'status' => TicketStatus::Escalated,
            'escalated_to' => $escalatedTo,
            'escalated_at' => now(),
        ]);
    }

    public function recordFirstResponse(): void
    {
        if (!$this->first_response_at) {
            $this->update(['first_response_at' => now()]);
        }
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', TicketStatus::Closed);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', TicketStatus::Open);
    }

    public function scopeOverdue($query)
    {
        return $query->whereNull('first_response_at')
            ->where('created_at', '<', now()->subHours(TicketPriority::Normal->responseTimeHours()));
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByCategory($query, TicketCategory $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByPriority($query, TicketPriority $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByAssignee($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeUrgent($query)
    {
        return $query->whereIn('priority', [TicketPriority::Urgent, TicketPriority::Critical]);
    }

    protected static function newFactory(): \App\Modules\CustomerService\Database\Factories\TicketFactory
    {
        return \App\Modules\CustomerService\Database\Factories\TicketFactory::new();
    }
}

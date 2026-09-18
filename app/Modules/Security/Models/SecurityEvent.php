<?php

namespace App\Modules\Security\Models;

use App\Modules\Customers\Models\Customer;
use App\Modules\Security\Enums\SecurityEventType;
use App\Modules\Security\Enums\SecurityLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SecurityEvent extends Model
{
    use HasFactory;

    protected $table = 'security_events';

    protected $fillable = [
        'event_type',
        'security_level',
        'user_id',
        'customer_id',
        'ip_address',
        'user_agent',
        'device_id',
        'location',
        'description',
        'details',
        'source',
        'is_resolved',
        'resolved_at',
        'resolved_by',
        'resolution_notes',
        'action_taken',
        'blocked',
        'blocked_until',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'event_type' => SecurityEventType::class,
            'security_level' => SecurityLevel::class,
            'details' => 'array',
            'metadata' => 'array',
            'is_resolved' => 'boolean',
            'blocked' => 'boolean',
            'resolved_at' => 'datetime',
            'blocked_until' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'resolved_by');
    }

    public function relatedEntity(): MorphTo
    {
        return $this->morphTo();
    }

    public function isCritical(): bool
    {
        return $this->event_type->isCritical();
    }

    public function requiresImmediateAction(): bool
    {
        return $this->event_type->requiresImmediateAction();
    }

    public function isBlocked(): bool
    {
        return $this->blocked && (!$this->blocked_until || $this->blocked_until->isFuture());
    }

    public function resolve(string $resolutionNotes, ?int $resolvedBy = null): void
    {
        $this->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => $resolvedBy ?? auth()->id(),
            'resolution_notes' => $resolutionNotes,
        ]);
    }

    public function block(\DateTime $until = null): void
    {
        $this->update([
            'blocked' => true,
            'blocked_until' => $until,
        ]);
    }

    public function unblock(): void
    {
        $this->update([
            'blocked' => false,
            'blocked_until' => null,
        ]);
    }

    public function scopeCritical($query)
    {
        return $query->where('security_level', SecurityLevel::Critical);
    }

    public function scopeHigh($query)
    {
        return $query->where('security_level', SecurityLevel::High);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    public function scopeBlocked($query)
    {
        return $query->where('blocked', true)
            ->where(function ($q) {
                $q->whereNull('blocked_until')
                    ->orWhere('blocked_until', '>', now());
            });
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByType($query, mixed $type)
    {
        $eventType = is_string($type) ? SecurityEventType::from($type) : $type;
        return $query->where('event_type', $eventType);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    protected static function newFactory(): \App\Modules\Security\Database\Factories\SecurityEventFactory
    {
        return \App\Modules\Security\Database\Factories\SecurityEventFactory::new();
    }
}

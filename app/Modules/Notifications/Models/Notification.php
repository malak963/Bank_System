<?php

namespace App\Modules\Notifications\Models;

use App\Modules\Customers\Models\Customer;
use App\Modules\Notifications\Enums\NotificationChannel;
use App\Modules\Notifications\Enums\NotificationStatus;
use App\Modules\Notifications\Enums\NotificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'customer_id',
        'notification_type',
        'channel',
        'status',
        'subject',
        'message',
        'data',
        'scheduled_at',
        'sent_at',
        'delivered_at',
        'read_at',
        'failed_at',
        'failure_reason',
        'retry_count',
        'priority',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'notification_type' => NotificationType::class,
            'channel' => NotificationChannel::class,
            'status' => NotificationStatus::class,
            'data' => 'array',
            'metadata' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
            'failed_at' => 'datetime',
            'priority' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isUrgent(): bool
    {
        return $this->notification_type->isUrgent();
    }

    public function isDelivered(): bool
    {
        return $this->status === NotificationStatus::Delivered;
    }

    public function isFailed(): bool
    {
        return $this->status === NotificationStatus::Failed;
    }

    public function isRead(): bool
    {
        return $this->status === NotificationStatus::Read;
    }

    public function canBeRetried(): bool
    {
        return $this->status->canBeRetried() && $this->retry_count < 3;
    }

    public function markAsRead(): void
    {
        $this->update([
            'status' => NotificationStatus::Read,
            'read_at' => now(),
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => NotificationStatus::Delivered,
            'delivered_at' => now(),
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => NotificationStatus::Failed,
            'failed_at' => now(),
            'failure_reason' => $reason,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', NotificationStatus::Pending);
    }

    public function scopeSent($query)
    {
        return $query->where('status', NotificationStatus::Sent);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', NotificationStatus::Delivered);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', NotificationStatus::Failed);
    }

    public function scopeUnread($query)
    {
        return $query->where('status', '!=', NotificationStatus::Read)->where('status', '!=', NotificationStatus::Delivered);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByType($query, mixed $type)
    {
        $notificationType = is_string($type) ? NotificationType::from($type) : $type;
        return $query->where('notification_type', $notificationType);
    }

    public function scopeByChannel($query, mixed $channel)
    {
        $notificationChannel = is_string($channel) ? NotificationChannel::from($channel) : $channel;
        return $query->where('channel', $notificationChannel);
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', '>=', 8);
    }

    public function scopeScheduled($query)
    {
        return $query->where('scheduled_at', '>', now());
    }

    public function scopeReadyToSend($query)
    {
        return $query->where('status', NotificationStatus::Pending)
            ->where(function ($q) {
                $q->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', now());
            });
    }

    protected static function newFactory(): \App\Modules\Notifications\Database\Factories\NotificationFactory
    {
        return \App\Modules\Notifications\Database\Factories\NotificationFactory::new();
    }
}

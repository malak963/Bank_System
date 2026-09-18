<?php

namespace App\Modules\Queues\Models;

use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Models\Customer;
use App\Modules\Queues\Enums\QueuePriority;
use App\Modules\Queues\Enums\QueueStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Queue extends Model
{
    use HasFactory;

    protected $table = 'queues';

    protected $fillable = [
        'branch_id',
        'customer_id',
        'ticket_number',
        'service_type',
        'status',
        'priority',
        'joined_at',
        'called_at',
        'serving_at',
        'completed_at',
        'estimated_wait_time',
        'actual_wait_time',
        'service_counter',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => QueueStatus::class,
            'priority' => QueuePriority::class,
            'joined_at' => 'datetime',
            'called_at' => 'datetime',
            'serving_at' => 'datetime',
            'completed_at' => 'datetime',
            'estimated_wait_time' => 'integer',
            'actual_wait_time' => 'integer',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isWaiting(): bool
    {
        return $this->status === QueueStatus::Waiting;
    }

    public function canBeCalled(): bool
    {
        return $this->status === QueueStatus::Waiting;
    }

    protected static function newFactory(): \App\Modules\Queues\Database\Factories\QueueFactory
    {
        return \App\Modules\Queues\Database\Factories\QueueFactory::new();
    }
}

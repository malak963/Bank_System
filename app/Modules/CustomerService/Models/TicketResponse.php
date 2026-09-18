<?php

namespace App\Modules\CustomerService\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketResponse extends Model
{
    use HasFactory;

    protected $table = 'ticket_responses';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'customer_id',
        'message',
        'is_internal',
        'attachments',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
            'attachments' => 'array',
            'metadata' => 'array',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(\App\Modules\Customers\Models\Customer::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    protected static function newFactory(): \App\Modules\CustomerService\Database\Factories\TicketResponseFactory
    {
        return \App\Modules\CustomerService\Database\Factories\TicketResponseFactory::new();
    }
}

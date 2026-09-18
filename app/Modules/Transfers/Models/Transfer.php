<?php

namespace App\Modules\Transfers\Models;

use App\Modules\Accounts\Models\Account;
use App\Modules\Customers\Models\Customer;
use App\Modules\Transfers\Enums\TransferStatus;
use App\Modules\Transfers\Enums\TransferType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transfer extends Model
{
    use HasFactory;

    protected $table = 'transfers';

    protected $fillable = [
        'transfer_reference',
        'from_account_id',
        'to_account_id',
        'customer_id',
        'transfer_type',
        'status',
        'amount',
        'currency',
        'exchange_rate',
        'converted_amount',
        'fees',
        'total_deducted',
        'recipient_name',
        'recipient_account',
        'recipient_bank',
        'recipient_bank_address',
        'swift_code',
        'iban',
        'routing_number',
        'reference',
        'description',
        'scheduled_for',
        'processed_at',
        'completed_at',
        'failed_at',
        'failure_reason',
        'cancelled_at',
        'cancellation_reason',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'transfer_type' => TransferType::class,
            'status' => TransferStatus::class,
            'amount' => 'decimal:2',
            'exchange_rate' => 'decimal:6',
            'converted_amount' => 'decimal:2',
            'fees' => 'decimal:2',
            'total_deducted' => 'decimal:2',
            'scheduled_for' => 'datetime',
            'processed_at' => 'datetime',
            'completed_at' => 'datetime',
            'failed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(\App\Modules\Transactions\Models\Transaction::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === TransferStatus::Completed;
    }

    public function isFailed(): bool
    {
        return $this->status === TransferStatus::Failed;
    }

    public function isScheduled(): bool
    {
        return $this->scheduled_for && $this->scheduled_for->isFuture();
    }

    public function scopeInternal($query)
    {
        return $query->where('transfer_type', TransferType::Internal);
    }

    public function scopeExternal($query)
    {
        return $query->where('transfer_type', TransferType::External);
    }

    public function scopeInternational($query)
    {
        return $query->where('transfer_type', TransferType::International);
    }

    public function scopePending($query)
    {
        return $query->where('status', TransferStatus::Pending);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', TransferStatus::Completed);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByAccount($query, $accountId)
    {
        return $query->where('from_account_id', $accountId)
            ->orWhere('to_account_id', $accountId);
    }

    public function scopeScheduled($query)
    {
        return $query->where('scheduled_for', '>', now());
    }

    protected static function newFactory(): \App\Modules\Transfers\Database\Factories\TransferFactory
    {
        return \App\Modules\Transfers\Database\Factories\TransferFactory::new();
    }
}

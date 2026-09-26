<?php

namespace App\Modules\Transactions\Models;

use App\Modules\Accounts\Models\Account;
use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Models\Customer;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Enums\TransactionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'transaction_reference',
        'branch_id',
        'customer_id',
        'account_id',
        'transaction_type',
        'amount',
        'balance_before',
        'balance_after',
        'currency',
        'status',
        'description',
        'reference_number',
        'related_transaction_id',
        'fees',
        'tax',
        'category',
        'sub_category',
        'tags',
        'metadata',
        'processed_at',
        'failed_at',
        'failed_reason',
        'reversed_at',
        'reversed_by',
        'reversal_reason',
        'created_by',
        'ip_address',
        'user_agent',
        'device_id',
        'transactable_type',
        'transactable_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
            'fees' => 'decimal:2',
            'tax' => 'decimal:2',
            'transaction_type' => TransactionType::class,
            'status' => TransactionStatus::class,
            'processed_at' => 'datetime',
            'failed_at' => 'datetime',
            'reversed_at' => 'datetime',
            'metadata' => 'array',
            'tags' => 'array',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function relatedTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'related_transaction_id');
    }

    public function relatedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'related_transaction_id');
    }

    public function reversals(): HasMany
    {
        return $this->hasMany(Transaction::class, 'related_transaction_id')
            ->where('transaction_type', TransactionType::Reversal);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function reversedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'reversed_by');
    }

    public function transactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function isCredit(): bool
    {
        return $this->transaction_type->isCredit();
    }

    public function isDebit(): bool
    {
        return $this->transaction_type->isDebit();
    }

    public function isCompleted(): bool
    {
        return $this->status === TransactionStatus::Completed;
    }

    public function isFailed(): bool
    {
        return $this->status === TransactionStatus::Failed;
    }

    public function canBeReversed(): bool
    {
        return $this->status->canBeReversed() && $this->transaction_type->isReversible();
    }

    public function scopeCredit($query)
    {
        return $query->where('transaction_type', TransactionType::Deposit)
            ->orWhere('transaction_type', TransactionType::Interest)
            ->orWhere('transaction_type', TransactionType::Refund);
    }

    public function scopeDebit($query)
    {
        return $query->where('transaction_type', TransactionType::Withdrawal)
            ->orWhere('transaction_type', TransactionType::Transfer)
            ->orWhere('transaction_type', TransactionType::Fee)
            ->orWhere('transaction_type', TransactionType::Penalty);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', TransactionStatus::Completed);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', TransactionStatus::Failed);
    }

    public function scopePending($query)
    {
        return $query->where('status', TransactionStatus::Pending);
    }

    protected static function newFactory(): \App\Modules\Transactions\Database\Factories\TransactionFactory
    {
        return \App\Modules\Transactions\Database\Factories\TransactionFactory::new();
    }
}

<?php

namespace App\Modules\BillsPayments\Models;

use App\Modules\BillsPayments\Enums\BillStatus;
use App\Modules\BillsPayments\Enums\BillType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'bill_reference',
        'customer_id',
        'account_id',
        'bill_type',
        'provider_name',
        'provider_account_number',
        'amount',
        'currency',
        'due_date',
        'status',
        'description',
        'metadata',
        'paid_at',
        'failed_at',
        'failed_reason',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'metadata' => 'array',
        'bill_type' => BillType::class,
        'status' => BillStatus::class,
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Modules\Customers\Models\Customer::class);
    }

    public function account()
    {
        return $this->belongsTo(\App\Modules\Accounts\Models\Account::class);
    }

    public function transaction()
    {
        return $this->belongsTo(\App\Modules\Transactions\Models\Transaction::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', BillStatus::PENDING);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', BillStatus::SCHEDULED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', BillStatus::COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', BillStatus::FAILED);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->where('status', '!=', BillStatus::COMPLETED);
    }
}

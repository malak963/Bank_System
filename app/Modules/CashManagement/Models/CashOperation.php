<?php

namespace App\Modules\CashManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashOperation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'operation_reference',
        'branch_id',
        'teller_id',
        'account_id',
        'operation_type',
        'amount',
        'currency',
        'status',
        'description',
        'notes',
        'approved_by',
        'approved_at',
        'completed_at',
        'operation_date',
        'counterparty_name',
        'counterparty_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'operation_date' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(\App\Modules\Branches\Models\Branch::class);
    }

    public function teller()
    {
        return $this->belongsTo(\App\Models\User::class, 'teller_id');
    }

    public function account()
    {
        return $this->belongsTo(\App\Modules\Accounts\Models\Account::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }
}

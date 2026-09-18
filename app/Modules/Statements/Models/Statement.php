<?php

namespace App\Modules\Statements\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Statement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'statement_reference',
        'account_id',
        'customer_id',
        'period_start',
        'period_end',
        'opening_balance',
        'closing_balance',
        'total_debits',
        'total_credits',
        'transaction_count',
        'currency',
        'status',
        'generated_at',
        'file_path',
        'file_size',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'total_debits' => 'decimal:2',
        'total_credits' => 'decimal:2',
        'generated_at' => 'datetime',
    ];

    public function account()
    {
        return $this->belongsTo(\App\Modules\Accounts\Models\Account::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Modules\Customers\Models\Customer::class);
    }

    public function transactions()
    {
        return $this->hasMany(\App\Modules\Transactions\Models\Transaction::class);
    }
}

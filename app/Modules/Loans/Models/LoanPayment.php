<?php

namespace App\Modules\Loans\Models;

use App\Models\User;
use App\Modules\Loans\Enums\LoanPaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanPayment extends Model
{
    protected $table = 'loan_payments';

    protected $fillable = [
        'loan_id',
        'payment_reference',
        'amount',
        'principal_amount',
        'interest_amount',
        'payment_method',
        'paid_at',
        'received_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'principal_amount' => 'decimal:2',
            'interest_amount' => 'decimal:2',
            'payment_method' => LoanPaymentMethod::class,
            'paid_at' => 'datetime',
        ];
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(LoanPaymentAllocation::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}

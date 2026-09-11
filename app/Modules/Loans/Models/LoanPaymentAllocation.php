<?php

namespace App\Modules\Loans\Models;

use App\Modules\Installments\Models\Installment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanPaymentAllocation extends Model
{
    protected $table = 'loan_payment_allocations';

    protected $fillable = [
        'loan_payment_id',
        'installment_id',
        'amount',
        'principal_amount',
        'interest_amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'principal_amount' => 'decimal:2',
            'interest_amount' => 'decimal:2',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(LoanPayment::class, 'loan_payment_id');
    }

    public function installment(): BelongsTo
    {
        return $this->belongsTo(Installment::class);
    }
}

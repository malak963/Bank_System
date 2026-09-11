<?php

namespace App\Modules\Installments\Models;

use App\Modules\Installments\Enums\InstallmentStatus;
use App\Modules\Loans\Models\Loan;
use App\Modules\Loans\Models\LoanPaymentAllocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Installment extends Model
{
    use HasFactory;

    protected $table = 'installments';

    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'principal_amount',
        'interest_amount',
        'amount_due',
        'principal_paid',
        'interest_paid',
        'amount_paid',
        'status',
        'paid_at',
        'last_payment_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'principal_amount' => 'decimal:2',
            'interest_amount' => 'decimal:2',
            'amount_due' => 'decimal:2',
            'principal_paid' => 'decimal:2',
            'interest_paid' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'status' => InstallmentStatus::class,
            'paid_at' => 'datetime',
            'last_payment_at' => 'datetime',
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

    public function remainingAmount(): float
    {
        return max(0, round((float) $this->amount_due - (float) $this->amount_paid, 2));
    }

    public function displayStatus(): InstallmentStatus
    {
        if ($this->status !== InstallmentStatus::Paid && $this->due_date?->isBefore(today())) {
            return InstallmentStatus::Overdue;
        }

        return $this->status;
    }

    protected static function newFactory(): \App\Modules\Installments\Database\Factories\InstallmentFactory
    {
        return \App\Modules\Installments\Database\Factories\InstallmentFactory::new();
    }
}

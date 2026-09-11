<?php

namespace App\Modules\Loans\Models;

use App\Models\User;
use App\Modules\Accounts\Models\Account;
use App\Modules\Customers\Models\Customer;
use App\Modules\Installments\Models\Installment;
use App\Modules\LoanTypes\Models\LoanType;
use App\Modules\Loans\Enums\InterestMethod;
use App\Modules\Loans\Enums\LoanStatus;
use App\Modules\Loans\Enums\RepaymentFrequency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $table = 'loans';

    protected $fillable = [
        'loan_reference',
        'customer_id',
        'account_id',
        'loan_type_id',
        'requested_amount',
        'approved_amount',
        'disbursed_amount',
        'annual_interest_rate',
        'term_months',
        'interest_method',
        'repayment_frequency',
        'purpose',
        'status',
        'application_date',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejection_reason',
        'disbursed_at',
        'first_payment_date',
        'total_interest',
        'total_payable',
        'total_paid',
        'outstanding_principal',
        'outstanding_interest',
        'next_payment_date',
    ];

    protected function casts(): array
    {
        return [
            'requested_amount' => 'decimal:2',
            'approved_amount' => 'decimal:2',
            'disbursed_amount' => 'decimal:2',
            'annual_interest_rate' => 'decimal:2',
            'total_interest' => 'decimal:2',
            'total_payable' => 'decimal:2',
            'total_paid' => 'decimal:2',
            'outstanding_principal' => 'decimal:2',
            'outstanding_interest' => 'decimal:2',
            'status' => LoanStatus::class,
            'interest_method' => InterestMethod::class,
            'repayment_frequency' => RepaymentFrequency::class,
            'application_date' => 'date',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'disbursed_at' => 'datetime',
            'first_payment_date' => 'date',
            'next_payment_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function loanType(): BelongsTo
    {
        return $this->belongsTo(LoanType::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(Installment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }

    protected static function newFactory(): \App\Modules\Loans\Database\Factories\LoanFactory
    {
        return \App\Modules\Loans\Database\Factories\LoanFactory::new();
    }
}

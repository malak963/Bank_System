<?php

namespace App\Modules\LoanTypes\Models;

use App\Modules\LoanTypes\Enums\LoanTypeStatus;
use App\Modules\Loans\Models\Loan;
use App\Modules\Loans\Enums\InterestMethod;
use App\Modules\Loans\Enums\RepaymentFrequency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoanType extends Model
{
    use HasFactory;

    protected $table = 'loan_types';

    protected $fillable = [
        'code',
        'name',
        'description',
        'currency',
        'minimum_amount',
        'maximum_amount',
        'minimum_term_months',
        'maximum_term_months',
        'annual_interest_rate',
        'interest_method',
        'repayment_frequency',
        'requires_collateral',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'minimum_amount' => 'decimal:2',
            'maximum_amount' => 'decimal:2',
            'annual_interest_rate' => 'decimal:2',
            'requires_collateral' => 'boolean',
            'status' => LoanTypeStatus::class,
            'interest_method' => InterestMethod::class,
            'repayment_frequency' => RepaymentFrequency::class,
        ];
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    protected static function newFactory(): \App\Modules\LoanTypes\Database\Factories\LoanTypeFactory
    {
        return \App\Modules\LoanTypes\Database\Factories\LoanTypeFactory::new();
    }
}

<?php

namespace App\Modules\Products\Models;

use App\Modules\Accounts\Models\Account;
use App\Modules\Branches\Models\Branch;
use App\Modules\Customers\Models\Customer;
use App\Modules\Products\Enums\ProductStatus;
use App\Modules\Products\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'product_number',
        'customer_id',
        'account_id',
        'branch_id',
        'product_type',
        'status',
        'name',
        'balance',
        'currency',
        'interest_rate',
        'term_months',
        'maturity_date',
        'auto_renew',
        'min_balance',
        'max_balance',
        'interest_earned',
        'last_interest_calculation',
        'opened_at',
        'closed_at',
        'notes',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'product_type' => ProductType::class,
            'status' => ProductStatus::class,
            'balance' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'interest_earned' => 'decimal:2',
            'min_balance' => 'decimal:2',
            'max_balance' => 'decimal:2',
            'auto_renew' => 'boolean',
            'maturity_date' => 'datetime',
            'last_interest_calculation' => 'datetime',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'metadata' => 'array',
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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(\App\Modules\Transactions\Models\Transaction::class);
    }

    public function isActive(): bool
    {
        return $this->status === ProductStatus::Active;
    }

    public function isMatured(): bool
    {
        return $this->status === ProductStatus::Matured || 
               ($this->maturity_date && $this->maturity_date->isPast());
    }

    public function canWithdraw(): bool
    {
        return $this->isActive() && $this->balance > $this->min_balance;
    }

    public function calculateInterest(): float
    {
        if (!$this->isActive() || !$this->interest_rate) {
            return 0;
        }

        $dailyRate = $this->interest_rate / 365 / 100;
        $daysSinceLastCalculation = $this->last_interest_calculation 
            ? $this->last_interest_calculation->diffInDays(now()) 
            : $this->opened_at->diffInDays(now());

        return $this->balance * $dailyRate * $daysSinceLastCalculation;
    }

    public function applyInterest(): void
    {
        $interest = $this->calculateInterest();
        
        if ($interest > 0) {
            $this->update([
                'balance' => $this->balance + $interest,
                'interest_earned' => $this->interest_earned + $interest,
                'last_interest_calculation' => now(),
            ]);
        }
    }

    public function close(?string $reason = null): void
    {
        if (!$this->canBeClosed()) {
            throw new \Exception('This product cannot be closed');
        }

        $this->update([
            'status' => ProductStatus::Closed,
            'closed_at' => now(),
            'notes' => $reason ?? $this->notes,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', ProductStatus::Active);
    }

    public function scopeMatured($query)
    {
        return $query->where('status', ProductStatus::Matured)
            ->orWhere('maturity_date', '<=', now());
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByType($query, ProductType $type)
    {
        return $query->where('product_type', $type);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('maturity_date', [
            now(),
            now()->addDays($days)
        ])->where('status', ProductStatus::Active);
    }

    protected static function newFactory(): \App\Modules\Products\Database\Factories\ProductFactory
    {
        return \App\Modules\Products\Database\Factories\ProductFactory::new();
    }
}

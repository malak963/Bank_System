<?php

namespace App\Modules\Accounts\Models;

use App\Modules\AccountTypes\Models\AccountType;
use App\Modules\Accounts\Enums\AccountStatus;
use App\Modules\Customers\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Modules\Loans\Models\Loan;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    protected $fillable = [
        'customer_id',
        'account_type_id',
        'account_number',
        'iban',
        'status',
        'balance',
        'opened_at',
        'closed_at',
        'frozen_at',
        'freeze_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => AccountStatus::class,
            'balance' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'frozen_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function isOpen(): bool
    {
        return $this->status === AccountStatus::Open;
    }

    protected static function newFactory(): \App\Modules\Accounts\Database\Factories\AccountFactory
    {
        return \App\Modules\Accounts\Database\Factories\AccountFactory::new();
    }
}

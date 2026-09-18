<?php

namespace App\Modules\AccountTypes\Models;

use App\Modules\AccountTypes\Enums\AccountTypeStatus;
use App\Modules\Accounts\Models\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountType extends Model
{
    use HasFactory;

    protected $table = 'account_types';

    protected $fillable = [
        'code',
        'name',
        'description',
        'currency',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => AccountTypeStatus::class,
        ];
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    protected static function newFactory(): \App\Modules\AccountTypes\Database\Factories\AccountTypeFactory
    {
        return \App\Modules\AccountTypes\Database\Factories\AccountTypeFactory::new();
    }
}

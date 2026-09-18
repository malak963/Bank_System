<?php

namespace App\Modules\Cards\Models;

use App\Modules\Accounts\Models\Account;
use App\Modules\Customers\Models\Customer;
use App\Modules\Cards\Enums\CardBrand;
use App\Modules\Cards\Enums\CardStatus;
use App\Modules\Cards\Enums\CardType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    use HasFactory;

    protected $table = 'cards';

    protected $fillable = [
        'card_number',
        'card_holder_name',
        'card_type',
        'card_brand',
        'expiry_month',
        'expiry_year',
        'cvv',
        'pin',
        'status',
        'account_id',
        'customer_id',
        'daily_limit',
        'monthly_limit',
        'international_enabled',
        'online_enabled',
        'contactless_enabled',
        'issued_at',
        'expires_at',
        'activated_at',
        'blocked_at',
        'block_reason',
        'last_used_at',
        'replacement_reason',
        'replaced_by_card_id',
        'metadata',
    ];

    protected $hidden = [
        'cvv',
        'pin',
    ];

    protected function casts(): array
    {
        return [
            'card_type' => CardType::class,
            'card_brand' => CardBrand::class,
            'status' => CardStatus::class,
            'daily_limit' => 'decimal:2',
            'monthly_limit' => 'decimal:2',
            'international_enabled' => 'boolean',
            'online_enabled' => 'boolean',
            'contactless_enabled' => 'boolean',
            'issued_at' => 'datetime',
            'expires_at' => 'datetime',
            'activated_at' => 'datetime',
            'blocked_at' => 'datetime',
            'last_used_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function replacementCard(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'replaced_by_card_id');
    }

    public function replacedCards(): HasMany
    {
        return $this->hasMany(Card::class, 'replaced_by_card_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(\App\Modules\Transactions\Models\Transaction::class);
    }

    public function maskCardNumber(): string
    {
        return '**** **** **** ' . substr($this->card_number, -4);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isActive(): bool
    {
        return $this->status === CardStatus::Active && !$this->isExpired();
    }

    public function canPerformTransactions(): bool
    {
        return $this->status->canPerformTransactions() && !$this->isExpired();
    }

    public function isBlocked(): bool
    {
        return $this->status->isBlocked();
    }

    public function scopeActive($query)
    {
        return $query->where('status', CardStatus::Active)
            ->where('expires_at', '>', now());
    }

    public function scopeByAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('expires_at', [
            now(),
            now()->addDays($days)
        ]);
    }

    protected static function newFactory(): \App\Modules\Cards\Database\Factories\CardFactory
    {
        return \App\Modules\Cards\Database\Factories\CardFactory::new();
    }
}

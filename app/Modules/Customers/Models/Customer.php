<?php

namespace App\Modules\Customers\Models;

use App\Models\User;
use App\Modules\Accounts\Models\Account;
use App\Modules\Branches\Models\Branch;
use App\Modules\Loans\Models\Loan;
use App\Modules\Notifications\Models\Notification;
use App\Modules\Security\Models\SecurityEvent;
use App\Modules\CustomerService\Models\Ticket;
use App\Modules\Products\Models\Product;
use App\Modules\Customers\Database\Factories\CustomerFactory;
use App\Modules\Customers\Enums\CustomerStatus;
use App\Modules\Customers\Enums\IdentityDocumentType;
use App\Modules\Customers\Enums\KycStatus;
use App\Modules\Customers\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    /** @use HasFactory<CustomerFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'user_id',
        'branch_id',
        'customer_number',
        'first_name',
        'last_name',
        'date_of_birth',
        'national_id',
        'identity_document_type',
        'identity_document_number',
        'identity_document_country',
        'identity_document_expires_at',
        'phone_number',
        'address',
        'status',
        'kyc_status',
        'kyc_reference',
        'kyc_reviewed_by',
        'kyc_reviewed_at',
        'kyc_rejection_reason',
        'risk_level',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'identity_document_type' => IdentityDocumentType::class,
            'identity_document_expires_at' => 'date',
            'status' => CustomerStatus::class,
            'kyc_status' => KycStatus::class,
            'kyc_reviewed_at' => 'datetime',
            'risk_level' => RiskLevel::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function kycReviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kyc_reviewed_by');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function securityEvents(): HasMany
    {
        return $this->hasMany(SecurityEvent::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected function fullName(): Attribute
    {
        return Attribute::get(
            fn (): string => trim("{$this->first_name} {$this->last_name}")
        );
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Modules\Customers\Models\Customer;
use App\Modules\Accounts\Models\Account;
use App\Modules\Users\Enums\UserRole;
use App\Modules\Security\Models\SecurityEvent;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Melbedran\RolePermession\Concerns\HasRoles;

#[Fillable(['name', 'email', 'phone', 'password', 'role', 'is_active', 'email_verified_at', 'last_login_at'])]
#[Hidden(['password', 'remember_token', 'two_factor_recovery_codes', 'two_factor_secret'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function accounts(): HasManyThrough
    {
        return $this->hasManyThrough(Account::class, Customer::class);
    }

    public function securityEvents(): HasMany
    {
        return $this->hasMany(SecurityEvent::class);
    }

    public function canManageUsers(): bool
    {
        return $this->role?->canManageUsers() ?? false;
    }

    public function isRolePermessionSuperAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function getSuperAdminAttribute(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function markLoggedIn(): void
    {
        $this->forceFill([
            'last_login_at' => now(),
        ])->save();
    }
}

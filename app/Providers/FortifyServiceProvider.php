<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Config::set('fortify.guard', 'web');
        Config::set('fortify.passwords', 'users');
        Config::set('fortify.home', '/dashboard');
        Config::set('fortify.prefix', '');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        // Explicit view registration
        Fortify::loginView(fn () => view('front.auth.login'));
        Fortify::twoFactorChallengeView(fn () => view('front.auth.two-factor-challenge'));
        Fortify::registerView(fn () => view('front.auth.register'));
        Fortify::requestPasswordResetLinkView(fn () => view('front.auth.forgot-password'));
        Fortify::resetPasswordView(fn (Request $request) => view('front.auth.reset-password', ['request' => $request]));

        // Authentication logic with banking-grade security (Email or Account Number + 3-attempt lockout)
        Fortify::authenticateUsing(function (Request $request) {
            $loginInput = trim($request->input('login') ?? $request->input('email') ?? '');
            $password = $request->input('password');

            // 1. Find user by email
            $user = User::where('email', $loginInput)->first();

            // 2. If not found by email, check by Customer Account Number or IBAN
            if (!$user) {
                $account = \App\Modules\Accounts\Models\Account::with('customer.user')
                    ->where('account_number', $loginInput)
                    ->orWhere('iban', $loginInput)
                    ->first();

                if ($account && $account->customer && $account->customer->user) {
                    $user = $account->customer->user;
                }
            }

            // 3. If user found and password matches
            if ($user && Hash::check($password, $user->password)) {
                if (isset($user->is_active) && !$user->is_active) {
                    throw ValidationException::withMessages([
                        Fortify::username() => [__('This account is locked or deactivated. Please visit your branch to reactivate or reset your PIN/password.')],
                    ]);
                }
                return $user;
            }

            // 4. Check if an Admin account is attempting to log in on customer portal
            $admin = Admin::where('email', $loginInput)->first();
            if ($admin && Hash::check($password, $admin->password)) {
                throw ValidationException::withMessages([
                    Fortify::username() => [__('This account belongs to an Administrator. Please use the Admin Portal to sign in.')],
                ]);
            }

            // 5. Track failed login attempt and log security audit event
            $throttleKey = Str::transliterate(Str::lower($loginInput).'|'.$request->ip());
            $currentAttempts = RateLimiter::attempts($throttleKey) + 1;

            try {
                \App\Modules\Security\Models\SecurityEvent::create([
                    'event_type' => \App\Modules\Security\Enums\SecurityEventType::FailedLogin,
                    'security_level' => $currentAttempts >= 3 
                        ? \App\Modules\Security\Enums\SecurityLevel::High 
                        : \App\Modules\Security\Enums\SecurityLevel::Medium,
                    'user_id' => $user?->id,
                    'customer_id' => $user?->customer?->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'description' => "Failed login attempt ({$currentAttempts}/3) for identifier: {$loginInput}",
                    'blocked' => $currentAttempts >= 3,
                    'blocked_until' => $currentAttempts >= 3 ? now()->addMinutes(15) : null,
                ]);
            } catch (\Throwable $e) {
                // Ignore if security logging table is not reachable
            }

            if ($currentAttempts >= 3) {
                throw ValidationException::withMessages([
                    Fortify::username() => [__('Your account has been locked after 3 consecutive failed login attempts. Please contact the branch or customer service to reset your credentials.')],
                ]);
            }

            $remaining = 3 - $currentAttempts;
            throw ValidationException::withMessages([
                Fortify::username() => [__('Invalid credentials. You have :count attempts remaining before account lockout.', ['count' => $remaining])],
            ]);
        });

        RateLimiter::for('login', function (Request $request) {
            $loginInput = $request->input('login') ?? $request->input(Fortify::username());
            $throttleKey = Str::transliterate(Str::lower($loginInput).'|'.$request->ip());

            return Limit::perMinutes(15, 3)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        RateLimiter::for('passkeys', function (Request $request) {
            $credentialId = $request->input('credential.id');

            return Limit::perMinute(10)->by(
                ($credentialId ?: $request->session()->getId()).'|'.$request->ip()
            );
        });
    }
}

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

        // Authentication logic with intelligent messaging
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                if (isset($user->is_active) && !$user->is_active) {
                    throw ValidationException::withMessages([
                        'email' => [__('This account is deactivated.')],
                    ]);
                }
                return $user;
            }

            // Check if this is an Admin account attempting to sign in on the user portal
            $admin = Admin::where('email', $request->email)->first();
            if ($admin && Hash::check($request->password, $admin->password)) {
                throw ValidationException::withMessages([
                    'email' => [__('This account belongs to an Administrator. Please use the Admin Portal to sign in.')],
                ]);
            }

            return null;
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
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

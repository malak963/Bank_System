<?php

use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Dashboard\TwoFactorAuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::get('/', function () {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $role = auth()->user()->role;
        if ($role === \App\Modules\Users\Enums\UserRole::Customer || (is_string($role) && $role === 'customer') || $role?->value === 'customer') {
            return redirect()->route('portal.dashboard');
        }
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', DashboardController::class)
        ->middleware(['auth:web,admin', 'not_customer'])
        ->name('dashboard');

    Route::get('/premium/dashboard', function () {
        return view('premium-dashboard');
    })->middleware(['auth:web,admin', 'not_customer'])
        ->name('premium.dashboard');

    // Localized Customer Authentication Routes
    Route::middleware('guest')->group(function (): void {
        Route::get('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store'])->name('login.store');
        Route::get('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
        Route::post('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store'])->name('register.store');
        Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('password.email');
        Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('/reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->name('password.store');
    });

    Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Admin Authentication Routes
    Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'store'])->name('admin.login.store');
    Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])->name('admin.logout');
    Route::get('/admin/two-factor-challenge', [AdminAuthController::class, 'createTwoFactor'])->name('admin.two-factor.login');
    Route::post('/admin/two-factor-challenge', [AdminAuthController::class, 'storeTwoFactor'])->name('admin.two-factor.login.store');

    Route::middleware('auth:web,admin')->group(function (): void {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Two-Factor Authentication Management
        Route::get('/2fa', [TwoFactorAuthenticationController::class, 'index'])->name('2fa.index');
        Route::get('/admin/2fa', [TwoFactorAuthenticationController::class, 'index'])->name('admin.2fa.index');
        Route::get('/user/2fa', [TwoFactorAuthenticationController::class, 'index'])->name('user.2fa.index');

        // Two-Factor Authentication Actions (Multi-guard sync)
        Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])->name('two-factor.enable');
        Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])->name('two-factor.disable');
        Route::post('/user/two-factor-recovery-codes', [TwoFactorAuthenticationController::class, 'regenerateRecoveryCodes'])->name('two-factor.regenerate-recovery-codes');

        // Email Verification & Password Confirmation
        Route::get('/verify-email', \App\Http\Controllers\Auth\EmailVerificationPromptController::class)->name('verification.notice');
        Route::get('/verify-email/{id}/{hash}', \App\Http\Controllers\Auth\VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
        Route::post('/email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');
        Route::get('/confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'show'])->name('password.confirm');
        Route::post('/confirm-password', [\App\Http\Controllers\Auth\ConfirmablePasswordController::class, 'store']);
    });
});

// Legacy redirects
Route::get('/user/login', function () {
    return redirect()->route('login');
});
Route::get('/user/two-factor-challenge', function () {
    return redirect()->route('two-factor.login');
});

// Field level auto-translation web endpoints (Google Translate AR <-> EN)
Route::post('/translate/field', [\App\Http\Controllers\Api\FieldTranslationController::class, 'translateField'])->name('web.translate.field');
Route::post('/translate/batch', [\App\Http\Controllers\Api\FieldTranslationController::class, 'translateBatch'])->name('web.translate.batch');

// Graceful redirect for trailing locale URLs (e.g. /bills-payments/ar -> /ar/bills-payments)
Route::get('{path}/{locale}', function (string $path, string $locale) {
    if ($locale === 'ar') {
        return redirect()->to(url('ar/' . $path), 301);
    }
    if ($locale === 'en') {
        return redirect()->to(url($path), 301);
    }
    abort(404);
})->where('path', '^[a-zA-Z0-9\-_]+$')->where('locale', '^(ar|en)$');

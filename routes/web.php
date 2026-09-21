<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::get('/', function () {
        return auth()->check()
            ? redirect()->route('dashboard')
            : redirect()->route('login');
    });

    Route::get('/dashboard', DashboardController::class)
        ->middleware(['auth:web,admin'])
        ->name('dashboard');

    Route::get('/premium/dashboard', function () {
        return view('premium-dashboard');
    })->middleware(['auth:web,admin'])
        ->name('premium.dashboard');

    Route::middleware('auth:web,admin')->group(function (): void {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Two-Factor Authentication Management
        Route::get('/2fa', function () {
            if (auth()->guard('admin')->check()) {
                return app(\App\Http\Controllers\Dashboard\TwoFactorAuthenticationController::class)->index();
            }
            return app(\App\Http\Controllers\User\TwoFactorAuthenticationController::class)->index();
        })->name('2fa.index');

        Route::get('/admin/2fa', [\App\Http\Controllers\Dashboard\TwoFactorAuthenticationController::class, 'index'])->name('admin.2fa.index');
        Route::get('/user/2fa', [\App\Http\Controllers\User\TwoFactorAuthenticationController::class, 'index'])->name('user.2fa.index');
    });

    // Fortify handles authentication routes (login, register, 2fa challenge, password resets)
    // require __DIR__.'/auth.php';
});

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

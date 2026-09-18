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
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    Route::get('/premium/dashboard', function () {
        return view('premium-dashboard');
    })->middleware(['auth', 'verified'])
        ->name('premium.dashboard');

    Route::middleware('auth')->group(function (): void {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    require __DIR__.'/auth.php';
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

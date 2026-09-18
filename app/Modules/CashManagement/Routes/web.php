<?php

use App\Modules\CashManagement\Controllers\CashManagementController;
use Illuminate\Support\Facades\Route;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::middleware(['web', 'auth'])
        ->prefix('cash-management')
        ->group(function (): void {
            Route::get('/', [CashManagementController::class, 'index'])->name('cash-management.index');
            Route::get('/create', [CashManagementController::class, 'create'])->name('cash-management.create');
            Route::post('/', [CashManagementController::class, 'store'])->name('cash-management.store');
            Route::get('/{id}', [CashManagementController::class, 'show'])->name('cash-management.show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [CashManagementController::class, 'edit'])->name('cash-management.edit')->where('id', '[0-9]+');
            Route::put('/{id}', [CashManagementController::class, 'update'])->name('cash-management.update')->where('id', '[0-9]+');
            Route::delete('/{id}', [CashManagementController::class, 'destroy'])->name('cash-management.destroy')->where('id', '[0-9]+');
        });
});

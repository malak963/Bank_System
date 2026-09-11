<?php

use App\Modules\Accounts\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])
    ->group(function (): void {
        Route::post('accounts/{account}/open', [AccountController::class, 'open'])->name('accounts.open');
        Route::post('accounts/{account}/close', [AccountController::class, 'close'])->name('accounts.close');
        Route::post('accounts/{account}/freeze', [AccountController::class, 'freeze'])->name('accounts.freeze');
        Route::post('accounts/{account}/reactivate', [AccountController::class, 'reactivate'])->name('accounts.reactivate');
        Route::resource('accounts', AccountController::class)->only(['index', 'create', 'store', 'show']);
    });

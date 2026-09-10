<?php

use App\Modules\AccountTypes\Controllers\AccountTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])
    ->group(function (): void {
        Route::resource('account-types', AccountTypeController::class)
            ->except(['show']);
    });

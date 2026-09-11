<?php

use App\Modules\LoanTypes\Controllers\LoanTypeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])
    ->group(function (): void {
        Route::resource('loan-types', LoanTypeController::class)->except(['show']);
    });

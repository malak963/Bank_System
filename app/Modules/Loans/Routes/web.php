<?php

use App\Modules\Loans\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])
    ->group(function (): void {
        Route::post('loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
        Route::post('loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
        Route::post('loans/{loan}/disburse', [LoanController::class, 'disburse'])->name('loans.disburse');
        Route::post('loans/{loan}/payments', [LoanController::class, 'payment'])->name('loans.payments.store');
        Route::resource('loans', LoanController::class)->only(['index', 'create', 'store', 'show']);
    });

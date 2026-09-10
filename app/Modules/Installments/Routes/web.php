<?php

use App\Modules\Installments\Controllers\InstallmentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])
    ->group(function (): void {
        Route::get('installments', [InstallmentController::class, 'index'])->name('installments.index');
    });

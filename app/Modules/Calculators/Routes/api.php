<?php

use App\Modules\Calculators\Controllers\LoanCalculatorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])
    ->group(function (): void {
        Route::post('calculators/loan/calculate', [LoanCalculatorController::class, 'calculate']);
        Route::post('calculators/loan/affordability', [LoanCalculatorController::class, 'affordability']);
    });

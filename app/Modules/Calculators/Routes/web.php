<?php

use App\Modules\Calculators\Controllers\LoanCalculatorController;
use Illuminate\Support\Facades\Route;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::middleware(['web', 'auth'])
        ->prefix('calculators')
        ->group(function (): void {
            Route::get('/', [LoanCalculatorController::class, 'index'])->name('calculators.index');
            Route::get('/loan', [LoanCalculatorController::class, 'loan'])->name('calculators.loan');
            Route::post('/loan/calculate', [LoanCalculatorController::class, 'calculateLoan'])->name('calculators.loan.calculate');
        });
});

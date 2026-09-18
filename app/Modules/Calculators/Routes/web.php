<?php

use App\Modules\Calculators\Controllers\LoanCalculatorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoanCalculatorController::class, 'index'])->name('calculators.index');
Route::get('/loan', [LoanCalculatorController::class, 'loan'])->name('calculators.loan');
Route::post('/loan/calculate', [LoanCalculatorController::class, 'calculateLoan'])->name('calculators.loan.calculate');

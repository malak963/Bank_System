<?php

use App\Modules\LoanTypes\Controllers\LoanTypeController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::middleware(['web', 'auth', 'verified'])
        ->group(function (): void {
            Route::resource('loan-types', LoanTypeController::class)->except(['show']);
        });
});

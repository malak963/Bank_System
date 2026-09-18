<?php

use App\Modules\Customers\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::middleware(['web', 'auth', 'verified'])
        ->group(function (): void {
            Route::resource(
                'customers',
                CustomerController::class
            );
        });
});

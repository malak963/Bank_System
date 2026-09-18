<?php

use App\Modules\AccountTypes\Controllers\AccountTypeController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::middleware(['web', 'auth', 'verified'])
        ->group(function (): void {
            Route::resource('account-types', AccountTypeController::class)
                ->except(['show']);
        });
});

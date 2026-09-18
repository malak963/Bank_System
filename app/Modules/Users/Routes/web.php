<?php

use App\Modules\Users\Controllers\UserController;
use App\Modules\Users\Middleware\EnsureCanManageUsers;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::middleware(['web', 'auth', 'verified', EnsureCanManageUsers::class])
        ->group(function (): void {
            Route::resource('users', UserController::class);
        });
});

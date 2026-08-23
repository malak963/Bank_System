<?php

use App\Modules\Customers\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])
    ->group(function (): void {
        Route::resource(
            'customers',
            CustomerController::class
        );
    });

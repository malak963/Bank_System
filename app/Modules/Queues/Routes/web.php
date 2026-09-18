<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])
    ->group(function (): void {
        // Queue web routes will be added here
    });

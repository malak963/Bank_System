<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api'])
    ->group(function (): void {
        // Appointment API routes will be added here
        Route::get('appointments/available-slots/{branch}', function () {
            return response()->json(['slots' => []]);
        });
    });

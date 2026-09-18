<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api'])
    ->group(function (): void {
        // Queue API routes will be added here
        Route::post('queues/join', function () {
            return response()->json(['ticket_number' => 'A001']);
        });
    });

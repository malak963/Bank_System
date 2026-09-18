<?php

use App\Modules\Branches\Controllers\BranchLocatorController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])
    ->group(function (): void {
        Route::get('branches/locator/nearby', [BranchLocatorController::class, 'nearby']);
        Route::get('branches/{branch}/queue/realtime', [BranchLocatorController::class, 'realtimeQueue']);
    });

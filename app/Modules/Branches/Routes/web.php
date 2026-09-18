<?php

use App\Modules\Branches\Controllers\BranchController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified'])
    ->group(function (): void {
        Route::post('branches/{branch}/open', [BranchController::class, 'open'])->name('branches.open');
        Route::post('branches/{branch}/close', [BranchController::class, 'close'])->name('branches.close');
        Route::resource('branches', BranchController::class);
    });

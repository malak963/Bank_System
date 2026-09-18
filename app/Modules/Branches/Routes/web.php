<?php

use App\Modules\Branches\Controllers\BranchController;
use Illuminate\Support\Facades\Route;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::middleware(['web', 'auth', 'verified'])
    ->group(function (): void {
        Route::post('branches/{branch}/open', [BranchController::class, 'open'])->name('branches.open');
        Route::post('branches/{branch}/close', [BranchController::class, 'close'])->name('branches.close');
        Route::resource('branches', BranchController::class);
    });
});

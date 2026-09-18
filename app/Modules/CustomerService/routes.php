<?php

use App\Modules\CustomerService\Controllers\CustomerServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('customer-service')->name('customerService.')->group(function () {
    Route::get('/', [CustomerServiceController::class, 'index'])->name('index');
    Route::get('/create', [CustomerServiceController::class, 'create'])->name('create');
    Route::post('/', [CustomerServiceController::class, 'store'])->name('store');
    Route::get('/{ticket}', [CustomerServiceController::class, 'show'])->name('show');
    Route::post('/{ticket}/assign', [CustomerServiceController::class, 'assign'])->name('assign');
    Route::post('/{ticket}/resolve', [CustomerServiceController::class, 'resolve'])->name('resolve');
    Route::post('/{ticket}/close', [CustomerServiceController::class, 'close'])->name('close');
    Route::post('/{ticket}/reopen', [CustomerServiceController::class, 'reopen'])->name('reopen');
    Route::post('/{ticket}/response', [CustomerServiceController::class, 'addResponse'])->name('add-response');
});

Route::prefix('api')->middleware(['api'])->group(function () {
    Route::prefix('customer-service')->name('api.customerService.')->group(function () {
        Route::get('/', [CustomerServiceController::class, 'apiIndex'])->name('index');
        Route::post('/', [CustomerServiceController::class, 'apiStore'])->name('store');
        Route::get('/{ticket}', [CustomerServiceController::class, 'apiShow'])->name('show');
        Route::get('/statistics', [CustomerServiceController::class, 'apiStatistics'])->name('statistics');
        Route::get('/overdue', [CustomerServiceController::class, 'apiOverdue'])->name('overdue');
        Route::get('/urgent', [CustomerServiceController::class, 'apiUrgent'])->name('urgent');
    });
});

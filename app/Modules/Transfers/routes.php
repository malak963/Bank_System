<?php

use App\Modules\Transfers\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

Route::prefix('transfers')->name('transfers.')->group(function () {
    Route::get('/', [TransferController::class, 'index'])->name('index');
    Route::get('/create', [TransferController::class, 'create'])->name('create');
    Route::post('/', [TransferController::class, 'store'])->name('store');
    Route::get('/{transfer}', [TransferController::class, 'show'])->name('show');
    Route::post('/{transfer}/cancel', [TransferController::class, 'cancel'])->name('cancel');
    Route::post('/{transfer}/retry', [TransferController::class, 'retry'])->name('retry');
    Route::post('/process-scheduled', [TransferController::class, 'processScheduled'])->name('process-scheduled');
});

Route::prefix('api')->middleware(['api'])->group(function () {
    Route::prefix('transfers')->name('api.transfers.')->group(function () {
        Route::get('/', [TransferController::class, 'apiIndex'])->name('index');
        Route::post('/', [TransferController::class, 'apiStore'])->name('store');
        Route::get('/{transfer}', [TransferController::class, 'apiShow'])->name('show');
        Route::get('/statistics', [TransferController::class, 'apiStatistics'])->name('statistics');
    });
});

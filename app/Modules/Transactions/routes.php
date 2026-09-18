<?php

use App\Modules\Transactions\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::prefix('transactions')->name('transactions.')->group(function () {
    Route::get('/', [TransactionController::class, 'index'])->name('index');
    Route::get('/create', [TransactionController::class, 'create'])->name('create');
    Route::post('/', [TransactionController::class, 'store'])->name('store');
    Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
    Route::get('/{transaction}/edit', [TransactionController::class, 'edit'])->name('edit');
    Route::put('/{transaction}', [TransactionController::class, 'update'])->name('update');
    Route::delete('/{transaction}', [TransactionController::class, 'destroy'])->name('destroy');
    Route::post('/{transaction}/reverse', [TransactionController::class, 'reverse'])->name('reverse');
});

Route::prefix('api')->middleware(['api'])->group(function () {
    Route::prefix('transactions')->name('api.transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'apiIndex'])->name('index');
        Route::post('/', [TransactionController::class, 'apiStore'])->name('store');
        Route::get('/{transaction}', [TransactionController::class, 'apiShow'])->name('show');
        Route::get('/account/{account}', [TransactionController::class, 'apiByAccount'])->name('by-account');
        Route::post('/{transaction}/reverse', [TransactionController::class, 'apiReverse'])->name('reverse');
    });
});

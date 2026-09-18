<?php

use App\Modules\BillsPayments\Controllers\BillsPaymentController;
use Illuminate\Support\Facades\Route;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::middleware(['web', 'auth'])
        ->prefix('bills-payments')
        ->group(function (): void {
            Route::get('/', [BillsPaymentController::class, 'index'])->name('bills-payments.index');
            Route::get('/create', [BillsPaymentController::class, 'create'])->name('bills-payments.create');
            Route::post('/', [BillsPaymentController::class, 'store'])->name('bills-payments.store');
            Route::get('/overdue', [BillsPaymentController::class, 'overdue'])->name('bills-payments.overdue');
            Route::get('/upcoming', [BillsPaymentController::class, 'upcoming'])->name('bills-payments.upcoming');
            Route::get('/{id}', [BillsPaymentController::class, 'show'])->name('bills-payments.show')->where('id', '[0-9]+');
            Route::get('/{id}/edit', [BillsPaymentController::class, 'edit'])->name('bills-payments.edit')->where('id', '[0-9]+');
            Route::put('/{id}', [BillsPaymentController::class, 'update'])->name('bills-payments.update')->where('id', '[0-9]+');
            Route::post('/{id}/pay', [BillsPaymentController::class, 'pay'])->name('bills-payments.pay')->where('id', '[0-9]+');
            Route::post('/{id}/schedule', [BillsPaymentController::class, 'schedule'])->name('bills-payments.schedule')->where('id', '[0-9]+');
            Route::post('/{id}/cancel', [BillsPaymentController::class, 'cancel'])->name('bills-payments.cancel')->where('id', '[0-9]+');
            Route::post('/{id}/refund', [BillsPaymentController::class, 'refund'])->name('bills-payments.refund')->where('id', '[0-9]+');
            Route::delete('/{id}', [BillsPaymentController::class, 'destroy'])->name('bills-payments.destroy')->where('id', '[0-9]+');
        });
});

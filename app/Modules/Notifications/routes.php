<?php

use App\Modules\Notifications\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/{notification}', [NotificationController::class, 'show'])->name('show');
    Route::post('/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::post('/retry-failed', [NotificationController::class, 'retryFailed'])->name('retry-failed');
    Route::post('/process-scheduled', [NotificationController::class, 'processScheduled'])->name('process-scheduled');
});
});

Route::prefix('api')->middleware(['api'])->group(function () {
    Route::prefix('notifications')->name('api.notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'apiIndex'])->name('index');
        Route::post('/', [NotificationController::class, 'apiCreate'])->name('create');
        Route::get('/{notification}', [NotificationController::class, 'apiShow'])->name('show');
        Route::get('/customer/{customer}', [NotificationController::class, 'apiByCustomer'])->name('by-customer');
        Route::post('/{notification}/mark-read', [NotificationController::class, 'apiMarkAsRead'])->name('mark-read');
        Route::post('/mark-all-read', [NotificationController::class, 'apiMarkAllAsRead'])->name('mark-all-read');
        Route::post('/{notification}/send', [NotificationController::class, 'apiSend'])->name('send');
    });
});

<?php

use App\Modules\Reports\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/create', [ReportController::class, 'create'])->name('create');
    Route::post('/', [ReportController::class, 'store'])->name('store');
    Route::get('/{report}', [ReportController::class, 'show'])->name('show');
    Route::get('/{report}/download', [ReportController::class, 'download'])->name('download');
    Route::post('/{report}/regenerate', [ReportController::class, 'regenerate'])->name('regenerate');
    Route::post('/process-scheduled', [ReportController::class, 'processScheduled'])->name('process-scheduled');
    Route::post('/cleanup-expired', [ReportController::class, 'cleanupExpired'])->name('cleanup-expired');
});
});

Route::prefix('api')->middleware(['api'])->group(function () {
    Route::prefix('reports')->name('api.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'apiIndex'])->name('index');
        Route::post('/', [ReportController::class, 'apiStore'])->name('store');
        Route::get('/{report}', [ReportController::class, 'apiShow'])->name('show');
        Route::post('/{report}/generate', [ReportController::class, 'apiGenerate'])->name('generate');
    });
});

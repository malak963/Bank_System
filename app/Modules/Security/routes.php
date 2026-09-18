<?php

use App\Modules\Security\Controllers\SecurityController;
use Illuminate\Support\Facades\Route;

Route::prefix('security')->name('security.')->group(function () {
    Route::get('/', [SecurityController::class, 'index'])->name('index');
    Route::get('/{event}', [SecurityController::class, 'show'])->name('show');
    Route::post('/{event}/resolve', [SecurityController::class, 'resolve'])->name('resolve');
    Route::post('/{event}/block', [SecurityController::class, 'block'])->name('block');
    Route::post('/{event}/unblock', [SecurityController::class, 'unblock'])->name('unblock');
    Route::post('/analyze', [SecurityController::class, 'analyze'])->name('analyze');
});

Route::prefix('api')->middleware(['api'])->group(function () {
    Route::prefix('security')->name('api.security.')->group(function () {
        Route::get('/', [SecurityController::class, 'apiIndex'])->name('index');
        Route::post('/', [SecurityController::class, 'apiLog'])->name('log');
        Route::get('/{event}', [SecurityController::class, 'apiShow'])->name('show');
        Route::post('/{event}/resolve', [SecurityController::class, 'apiResolve'])->name('resolve');
        Route::get('/critical', [SecurityController::class, 'apiCritical'])->name('critical');
        Route::get('/fraud-alerts', [SecurityController::class, 'apiFraudAlerts'])->name('fraud-alerts');
        Route::post('/analyze', [SecurityController::class, 'apiAnalyze'])->name('analyze');
    });
});

<?php

use App\Modules\Cards\Controllers\CardController;
use Illuminate\Support\Facades\Route;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::prefix('cards')->name('cards.')->group(function () {
    Route::get('/', [CardController::class, 'index'])->name('index');
    Route::get('/create', [CardController::class, 'create'])->name('create');
    Route::post('/', [CardController::class, 'store'])->name('store');
    Route::get('/{card}', [CardController::class, 'show'])->name('show');
    Route::post('/{card}/activate', [CardController::class, 'activate'])->name('activate');
    Route::post('/{card}/block', [CardController::class, 'block'])->name('block');
    Route::post('/{card}/unblock', [CardController::class, 'unblock'])->name('unblock');
    Route::post('/{card}/replace', [CardController::class, 'replace'])->name('replace');
    Route::post('/{card}/update-pin', [CardController::class, 'updatePin'])->name('update-pin');
    Route::post('/{card}/update-limits', [CardController::class, 'updateLimits'])->name('update-limits');
    Route::post('/{card}/toggle-features', [CardController::class, 'toggleFeatures'])->name('toggle-features');
});
});

Route::prefix('api')->middleware(['api'])->group(function () {
    Route::prefix('cards')->name('api.cards.')->group(function () {
        Route::get('/', [CardController::class, 'apiIndex'])->name('index');
        Route::post('/', [CardController::class, 'apiStore'])->name('store');
        Route::get('/{card}', [CardController::class, 'apiShow'])->name('show');
        Route::get('/account/{account}', [CardController::class, 'apiByAccount'])->name('by-account');
        Route::post('/{card}/activate', [CardController::class, 'apiActivate'])->name('activate');
        Route::post('/{card}/block', [CardController::class, 'apiBlock'])->name('block');
    });
});

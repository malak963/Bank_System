<?php

use App\Modules\Products\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    Route::post('/{product}/close', [ProductController::class, 'close'])->name('close');
    Route::post('/{product}/apply-interest', [ProductController::class, 'applyInterest'])->name('apply-interest');
});
});

Route::prefix('api')->middleware(['api'])->group(function () {
    Route::prefix('products')->name('api.products.')->group(function () {
        Route::get('/', [ProductController::class, 'apiIndex'])->name('index');
        Route::post('/', [ProductController::class, 'apiStore'])->name('store');
        Route::get('/{product}', [ProductController::class, 'apiShow'])->name('show');
        Route::get('/statistics', [ProductController::class, 'apiStatistics'])->name('statistics');
    });
});

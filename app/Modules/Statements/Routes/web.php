<?php

use App\Modules\Statements\Controllers\StatementController;
use Illuminate\Support\Facades\Route;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function (): void {
    Route::middleware(['web', 'auth'])
        ->prefix('statements')
        ->group(function (): void {
            Route::get('/', [StatementController::class, 'index'])->name('statements.index');
            Route::get('/create', [StatementController::class, 'create'])->name('statements.create');
            Route::post('/', [StatementController::class, 'store'])->name('statements.store');
            Route::get('/{id}', [StatementController::class, 'show'])->name('statements.show')->where('id', '[0-9]+');
            Route::post('/{id}/generate', [StatementController::class, 'generate'])->name('statements.generate')->where('id', '[0-9]+');
            Route::get('/{id}/download', [StatementController::class, 'download'])->name('statements.download')->where('id', '[0-9]+');
            Route::delete('/{id}', [StatementController::class, 'destroy'])->name('statements.destroy')->where('id', '[0-9]+');
        });
});

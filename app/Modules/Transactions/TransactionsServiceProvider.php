<?php

namespace App\Modules\Transactions;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class TransactionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Transactions\Services\TransactionService::class,
            fn () => new \App\Modules\Transactions\Services\TransactionService()
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'transactions');
        
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        Route::middleware(['web', 'auth'])
            ->group(base_path('app/Modules/Transactions/routes.php'));
    }
}

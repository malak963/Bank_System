<?php

namespace App\Modules\BillsPayments;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class BillsPaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\BillsPayments\Services\BillsPaymentService::class,
            fn () => new \App\Modules\BillsPayments\Services\BillsPaymentService(
                app(\App\Modules\Transactions\Services\TransactionService::class)
            )
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'bills-payments');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        Route::middleware(['web'])
            ->prefix('bills-payments')
            ->group(base_path('app/Modules/BillsPayments/Routes/web.php'));
    }
}

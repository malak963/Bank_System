<?php

namespace App\Modules\Transfers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class TransfersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Transfers\Services\TransferService::class,
            fn () => new \App\Modules\Transfers\Services\TransferService()
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'transfers');
        
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        Route::middleware(['web', 'auth'])
            ->group(base_path('app/Modules/Transfers/routes.php'));
    }
}

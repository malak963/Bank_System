<?php

namespace App\Modules\CashManagement;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CashManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\CashManagement\Services\CashManagementService::class,
            fn () => new \App\Modules\CashManagement\Services\CashManagementService()
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'cash-management');
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
    }
}

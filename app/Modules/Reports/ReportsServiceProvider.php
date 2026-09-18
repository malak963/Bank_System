<?php

namespace App\Modules\Reports;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ReportsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Reports\Services\ReportService::class,
            fn () => new \App\Modules\Reports\Services\ReportService()
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'reports');
        
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        Route::middleware(['web', 'auth'])
            ->group(base_path('app/Modules/Reports/routes.php'));
    }
}

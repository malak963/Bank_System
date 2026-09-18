<?php

namespace App\Modules\CustomerService;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CustomerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\CustomerService\Services\CustomerServiceService::class,
            fn () => new \App\Modules\CustomerService\Services\CustomerServiceService()
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'customerService');
        
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        Route::middleware(['web', 'auth'])
            ->group(base_path('app/Modules/CustomerService/routes.php'));
    }
}

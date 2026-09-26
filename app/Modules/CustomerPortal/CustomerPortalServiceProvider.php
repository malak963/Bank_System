<?php

namespace App\Modules\CustomerPortal;

use App\Modules\CustomerPortal\Services\CustomerPortalService;
use Illuminate\Support\ServiceProvider;

class CustomerPortalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CustomerPortalService::class, function () {
            return new CustomerPortalService();
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'customer-portal');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }
}

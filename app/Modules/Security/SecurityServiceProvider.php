<?php

namespace App\Modules\Security;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class SecurityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Security\Services\SecurityService::class,
            fn () => new \App\Modules\Security\Services\SecurityService()
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'security');
        
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        Route::middleware(['web', 'auth'])
            ->group(base_path('app/Modules/Security/routes.php'));
    }
}

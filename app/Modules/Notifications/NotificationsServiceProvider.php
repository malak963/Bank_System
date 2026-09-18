<?php

namespace App\Modules\Notifications;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class NotificationsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Notifications\Services\NotificationService::class,
            fn () => new \App\Modules\Notifications\Services\NotificationService()
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'notifications');
        
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        Route::middleware(['web', 'auth'])
            ->group(base_path('app/Modules/Notifications/routes.php'));
    }
}

<?php

namespace App\Modules\Cards;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CardsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Cards\Services\CardService::class,
            fn () => new \App\Modules\Cards\Services\CardService()
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'cards');
        
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        Route::middleware(['web', 'auth'])
            ->group(base_path('app/Modules/Cards/routes.php'));
    }
}

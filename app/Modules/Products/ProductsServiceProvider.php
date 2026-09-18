<?php

namespace App\Modules\Products;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ProductsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Products\Services\ProductService::class,
            fn () => new \App\Modules\Products\Services\ProductService()
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'products');
        
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        Route::middleware(['web', 'auth'])
            ->group(base_path('app/Modules/Products/routes.php'));
    }
}

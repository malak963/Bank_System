<?php

namespace App\Modules\Statements;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class StatementsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Statements\Services\StatementService::class,
            fn () => new \App\Modules\Statements\Services\StatementService()
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'statements');
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
    }
}

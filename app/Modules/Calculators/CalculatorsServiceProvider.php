<?php

namespace App\Modules\Calculators;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class CalculatorsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \App\Modules\Calculators\Services\LoanCalculatorService::class,
            fn () => new \App\Modules\Calculators\Services\LoanCalculatorService()
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'calculators');
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
    }
}

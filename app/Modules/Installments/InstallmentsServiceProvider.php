<?php

namespace App\Modules\Installments;

use App\Modules\Installments\Contracts\InstallmentRepositoryContract;
use App\Modules\Installments\Repositories\InstallmentRepository;
use Illuminate\Support\ServiceProvider;

class InstallmentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InstallmentRepositoryContract::class, InstallmentRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'installments');
    }
}

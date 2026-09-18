<?php

namespace App\Modules\Customers;

use App\Modules\Customers\Contracts\CustomerRepositoryContract;
use App\Modules\Customers\Repositories\CustomerRepository;
use Illuminate\Support\ServiceProvider;

class CustomerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CustomerRepositoryContract::class, CustomerRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'customers');
    }
}

<?php

namespace App\Modules\Accounts;

use App\Modules\Accounts\Contracts\AccountRepositoryContract;
use App\Modules\Accounts\Repositories\AccountRepository;
use Illuminate\Support\ServiceProvider;

class AccountsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AccountRepositoryContract::class, AccountRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'accounts');
    }
}

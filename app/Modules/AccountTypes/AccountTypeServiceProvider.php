<?php

namespace App\Modules\AccountTypes;

use App\Modules\AccountTypes\Contracts\AccountTypeRepositoryContract;
use App\Modules\AccountTypes\Repositories\AccountTypeRepository;
use Illuminate\Support\ServiceProvider;

class AccountTypeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AccountTypeRepositoryContract::class, AccountTypeRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'account_types');
    }
}

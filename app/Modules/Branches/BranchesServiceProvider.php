<?php

namespace App\Modules\Branches;

use App\Modules\Branches\Contracts\BranchRepositoryContract;
use App\Modules\Branches\Repositories\BranchRepository;
use Illuminate\Support\ServiceProvider;

class BranchesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BranchRepositoryContract::class, BranchRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'branches');
    }
}

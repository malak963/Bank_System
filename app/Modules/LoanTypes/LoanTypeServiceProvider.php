<?php

namespace App\Modules\LoanTypes;

use App\Modules\LoanTypes\Contracts\LoanTypeRepositoryContract;
use App\Modules\LoanTypes\Repositories\LoanTypeRepository;
use Illuminate\Support\ServiceProvider;

class LoanTypeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoanTypeRepositoryContract::class, LoanTypeRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'loan_types');
    }
}

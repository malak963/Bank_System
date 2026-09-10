<?php

namespace App\Modules\Loans;

use App\Modules\Loans\Contracts\LoanRepositoryContract;
use App\Modules\Loans\Repositories\LoanRepository;
use Illuminate\Support\ServiceProvider;

class LoansServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LoanRepositoryContract::class, LoanRepository::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
        $this->loadViewsFrom(__DIR__.'/Views', 'loans');
    }
}

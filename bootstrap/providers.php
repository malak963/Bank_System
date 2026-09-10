<?php

use App\Modules\AccountTypes\AccountTypeServiceProvider;
use App\Modules\Accounts\AccountsServiceProvider;
use App\Modules\Customers\CustomerServiceProvider;
use App\Modules\Installments\InstallmentsServiceProvider;
use App\Modules\Loans\LoansServiceProvider;
use App\Modules\LoanTypes\LoanTypeServiceProvider;
use App\Modules\Users\UserServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    AccountTypeServiceProvider::class,
    AccountsServiceProvider::class,
    CustomerServiceProvider::class,
    InstallmentsServiceProvider::class,
    LoanTypeServiceProvider::class,
    LoansServiceProvider::class,
    UserServiceProvider::class,
];

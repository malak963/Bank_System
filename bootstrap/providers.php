<?php

use App\Modules\AccountTypes\AccountTypeServiceProvider;
use App\Modules\Accounts\AccountsServiceProvider;
use App\Modules\Appointments\AppointmentsServiceProvider;
use App\Modules\Branches\BranchesServiceProvider;
use App\Modules\Calculators\CalculatorsServiceProvider;
use App\Modules\CashManagement\CashManagementServiceProvider;
use App\Modules\Customers\CustomerServiceProvider as CustomersServiceProvider;
use App\Modules\Installments\InstallmentsServiceProvider;
use App\Modules\Loans\LoansServiceProvider;
use App\Modules\LoanTypes\LoanTypeServiceProvider;
use App\Modules\Queues\QueuesServiceProvider;
use App\Modules\Transactions\TransactionsServiceProvider;
use App\Modules\Cards\CardsServiceProvider;
use App\Modules\Notifications\NotificationsServiceProvider;
use App\Modules\Reports\ReportsServiceProvider;
use App\Modules\Security\SecurityServiceProvider;
use App\Modules\CustomerService\CustomerServiceProvider;
use App\Modules\Products\ProductsServiceProvider;
use App\Modules\Transfers\TransfersServiceProvider;
use App\Modules\Users\UserServiceProvider;
use App\Modules\BillsPayments\BillsPaymentServiceProvider;
use App\Modules\Statements\StatementsServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    AccountTypeServiceProvider::class,
    AccountsServiceProvider::class,
    AppointmentsServiceProvider::class,
    BranchesServiceProvider::class,
    CalculatorsServiceProvider::class,
    CashManagementServiceProvider::class,
    CustomersServiceProvider::class,
    InstallmentsServiceProvider::class,
    LoanTypeServiceProvider::class,
    LoansServiceProvider::class,
    QueuesServiceProvider::class,
    TransactionsServiceProvider::class,
    CardsServiceProvider::class,
    NotificationsServiceProvider::class,
    ReportsServiceProvider::class,
    SecurityServiceProvider::class,
    CustomerServiceProvider::class,
    ProductsServiceProvider::class,
    TransfersServiceProvider::class,
    UserServiceProvider::class,
    BillsPaymentServiceProvider::class,
    StatementsServiceProvider::class,
];

<?php

use App\Modules\Customers\CustomerServiceProvider;
use App\Modules\Users\UserServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    CustomerServiceProvider::class,
    UserServiceProvider::class,
];

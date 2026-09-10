<?php

return [
    'primary' => [
        ['route' => 'dashboard', 'label' => 'Dashboard', 'active' => 'dashboard'],
        ['route' => 'customers.index', 'label' => 'Customers', 'active' => 'customers.*'],
        ['route' => 'accounts.index', 'label' => 'Accounts', 'active' => 'accounts.*'],
        ['route' => 'account-types.index', 'label' => 'Account Types', 'active' => 'account-types.*'],
        ['route' => 'loans.index', 'label' => 'Loans', 'active' => 'loans.*'],
        ['route' => 'installments.index', 'label' => 'Installments', 'active' => 'installments.*'],
        ['route' => 'users.index', 'label' => 'Users', 'active' => 'users.*', 'ability' => 'canManageUsers'],
    ],
    'profile_route' => 'profile.edit',
    'logout_route' => 'logout',
];

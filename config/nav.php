<?php

return [
    'primary' => [
        [
            'route' => 'dashboard',
            'label' => 'Dashboard',
            'active' => 'dashboard',
            'icon' => 'layout-dashboard',
        ],

        [
            'route' => 'premium.dashboard',
            'label' => 'Premium Experience',
            'active' => 'premium.*',
            'icon' => 'sparkles',
        ],

        [
            'route' => 'customers.index',
            'label' => 'Customers',
            'active' => 'customers.*',
            'icon' => 'users-round',
        ],

        [
            'route' => 'branches.index',
            'label' => 'Branches',
            'active' => 'branches.*',
            'icon' => 'building-2',
        ],

        [
            'route' => 'accounts.index',
            'label' => 'Accounts',
            'active' => 'accounts.*',
            'icon' => 'wallet-cards',
        ],

        [
            'route' => 'account-types.index',
            'label' => 'Account Types',
            'active' => 'account-types.*',
            'icon' => 'layers-3',
        ],

        [
            'route' => 'loans.index',
            'label' => 'Loans',
            'active' => 'loans.*',
            'icon' => 'hand-coins',
        ],

        [
            'route' => 'installments.index',
            'label' => 'Installments',
            'active' => 'installments.*',
            'icon' => 'calendar-check-2',
        ],

        [
            'route' => 'users.index',
            'label' => 'Users',
            'active' => 'users.*',
            'ability' => 'canManageUsers',
            'icon' => 'shield-user',
        ],
    ],

    'profile_route' => 'profile.edit',

    'logout_route' => 'logout',
];
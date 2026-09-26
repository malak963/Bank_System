<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User (Customer) Portal Navigation Configuration
    |--------------------------------------------------------------------------
    */
    'brand' => [
        'name' => 'MDAD Bank',
        'logo_route' => 'portal.dashboard',
    ],

    'links' => [
        [
            'title' => 'Dashboard',
            'route' => 'portal.dashboard',
            'active' => 'portal.dashboard',
        ],
        [
            'title' => 'Deposit',
            'route' => 'portal.deposit',
            'active' => 'portal.deposit*',
        ],
        [
            'title' => 'Withdraw',
            'route' => 'portal.withdraw',
            'active' => 'portal.withdraw*',
        ],
        [
            'title' => 'Transfer',
            'route' => 'portal.transfer',
            'active' => 'portal.transfer*',
        ],
        [
            'title' => 'Invoices',
            'route' => 'portal.invoices',
            'active' => 'portal.invoices*',
        ],
        [
            'title' => 'Transactions',
            'route' => 'portal.transactions',
            'active' => 'portal.transactions*',
        ],
    ],

    'profile_menu' => [
        [
            'title' => 'Profile',
            'route' => 'profile.edit',
        ],
        [
            'title' => 'Two-Factor Authentication',
            'route' => 'portal.2fa',
        ],
    ],
];

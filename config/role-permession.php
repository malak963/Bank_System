<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Abilities catalog (code => label)
    |--------------------------------------------------------------------------
    */
    'abilities' => [
        // Users & Roles Management
        'users.view' => 'View Users',
        'users.create' => 'Create Users',
        'users.update' => 'Update Users',
        'users.delete' => 'Deactivate / Delete Users',
        'roles.view' => 'View Roles',
        'roles.create' => 'Create Roles',
        'roles.update' => 'Update Roles',
        'roles.delete' => 'Delete Roles',

        // Branches
        'branches.view' => 'View Branches',
        'branches.create' => 'Create Branches',
        'branches.update' => 'Update Branches',
        'branches.delete' => 'Delete Branches',

        // Customers & KYC
        'customers.view' => 'View Customers',
        'customers.create' => 'Register Customer',
        'customers.update' => 'Update Customer Information',
        'customers.kyc_review' => 'Review & Approve KYC Documents',
        'customers.delete' => 'Delete / Archive Customer',

        // Accounts & Account Types
        'accounts.view' => 'View Accounts',
        'accounts.create' => 'Open New Account',
        'accounts.freeze' => 'Freeze / Unfreeze Account',
        'accounts.close' => 'Close Account',
        'account_types.manage' => 'Manage Account Types',

        // Transactions
        'transactions.view' => 'View Transactions',
        'transactions.create' => 'Create Transactions (Deposits & Withdrawals)',
        'transactions.reverse' => 'Reverse Transactions',

        // Transfers
        'transfers.view' => 'View Transfers',
        'transfers.create' => 'Execute Transfers',
        'transfers.cancel' => 'Cancel Pending Transfers',
        'transfers.retry' => 'Retry Failed Transfers',

        // Cards
        'cards.view' => 'View Cards',
        'cards.create' => 'Issue New Card',
        'cards.activate' => 'Activate Card',
        'cards.block' => 'Block / Unblock Card',
        'cards.pin_manage' => 'Manage / Reset Card PIN',
        'cards.limits' => 'Manage Card Spending Limits',

        // Loans & Loan Types
        'loans.view' => 'View Loans',
        'loans.create' => 'Create Loan Application',
        'loans.approve' => 'Approve / Reject Loans',
        'loans.disburse' => 'Disburse Loans',
        'loans.payments' => 'Process Installment Payments',
        'loan_types.manage' => 'Manage Loan Types',

        // Cash & Vault Management
        'cash.view' => 'View Cash Operations',
        'cash.operate' => 'Perform Teller Cash In/Out',
        'cash.close_vault' => 'Close Vault / Balance Cash',
        'cash.audit' => 'Audit Cash Operations',

        // Products
        'products.view' => 'View Products',
        'products.manage' => 'Manage Products',
        'products.apply_interest' => 'Apply Product Interest',

        // Bills & Statements
        'bills.view' => 'View Bills',
        'bills.pay' => 'Pay Bills',
        'statements.generate' => 'Generate & Download Statements',

        // Customer Service & Front Office
        'tickets.view' => 'View Support Tickets',
        'tickets.respond' => 'Respond & Resolve Tickets',
        'queues.manage' => 'Manage Queues & Calling System',
        'appointments.manage' => 'Manage Appointments',

        // Reports
        'reports.view' => 'View Reports',
        'reports.generate' => 'Generate Reports',
        'reports.download' => 'Download Reports',

        // Security & Compliance
        'security.view' => 'View Security Events',
        'security.block' => 'Block Malicious Users/IPs',
        'security.resolve' => 'Resolve Security Incidents',
    ],

    /*
    |--------------------------------------------------------------------------
    | Super-admin column on the user model (null = disabled)
    |--------------------------------------------------------------------------
    */
    'super_admin_column' => 'super_admin',

    'models' => [
        'role' => Melbedran\RolePermession\Models\Role::class,
        'role_ability' => Melbedran\RolePermession\Models\RoleAbility::class,
    ],

    'tables' => [
        'roles' => 'roles',
        'role_abilities' => 'role_abilities',
        'role_user' => 'role_user',
    ],

    'morph' => 'authorizable',

    'register_gates' => true,

    /*
    |--------------------------------------------------------------------------
    | Load package migrations automatically
    |--------------------------------------------------------------------------
    */
    'load_migrations' => true,

    'blade' => [
        'enabled' => true,
        'can' => 'canAbility',
        'cannot' => 'cannotAbility',
        'canAny' => 'canAnyAbility',
        'canAll' => 'canAllAbility',
    ],

    /*
    |--------------------------------------------------------------------------
    | Built-in admin UI (Blade)
    |--------------------------------------------------------------------------
    */
    'ui' => [
        'enabled' => true,
        'prefix' => 'admin/roles',
        'middleware' => ['web', 'auth'],
        'route_name_prefix' => 'role-permession.',
    ],

];

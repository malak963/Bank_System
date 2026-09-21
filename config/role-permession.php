<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Abilities catalog (code => label)
    |--------------------------------------------------------------------------
    */
    'abilities' => [
        'roles.view' => 'View Roles',
        'roles.create' => 'Create Roles',
        'roles.update' => 'Update Roles',
        'roles.delete' => 'Delete Roles',
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

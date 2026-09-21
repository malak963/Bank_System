<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Modules\Users\Enums\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin in admins table
        Admin::updateOrCreate(
            ['email' => 'admin@bank.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'status' => true,
                'is_super_admin' => true,
            ]
        );

        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@bank.com'],
            [
                'name' => 'Bank Admin',
                'phone' => '+963911111111',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Branch Manager user
        User::updateOrCreate(
            ['email' => 'manager@bank.com'],
            [
                'name' => 'Branch Manager',
                'phone' => '+963922222222',
                'password' => Hash::make('password'),
                'role' => UserRole::Manager,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Employee user
        User::updateOrCreate(
            ['email' => 'employee@bank.com'],
            [
                'name' => 'Bank Employee',
                'phone' => '+963933333333',
                'password' => Hash::make('password'),
                'role' => UserRole::Employee,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Customer user
        User::updateOrCreate(
            ['email' => 'customer@bank.com'],
            [
                'name' => 'Standard Customer',
                'phone' => '+963944444444',
                'password' => Hash::make('password'),
                'role' => UserRole::Customer,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}

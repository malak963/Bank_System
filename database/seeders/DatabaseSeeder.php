<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use App\Modules\Users\Enums\UserRole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Melbedran\RolePermession\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Roles & Permissions
        $this->call(RoleAndPermissionSeeder::class);

        $managerRole = Role::where('name', 'Branch Manager')->first();
        $tellerRole = Role::where('name', 'Teller')->first();
        $loanOfficerRole = Role::where('name', 'Loan Officer')->first();
        $csRole = Role::where('name', 'Customer Service')->first();
        $auditorRole = Role::where('name', 'Auditor')->first();

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

        // Admin user (Super Admin)
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
        $manager = User::updateOrCreate(
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
        if ($managerRole) {
            $manager->roles()->sync([$managerRole->id]);
        }

        // Bank Teller user
        $teller = User::updateOrCreate(
            ['email' => 'teller@bank.com'],
            [
                'name' => 'Bank Teller',
                'phone' => '+963933333334',
                'password' => Hash::make('password'),
                'role' => UserRole::Employee,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if ($tellerRole) {
            $teller->roles()->sync([$tellerRole->id]);
        }

        // Loan Officer user
        $loanOfficer = User::updateOrCreate(
            ['email' => 'loan_officer@bank.com'],
            [
                'name' => 'Loan Officer',
                'phone' => '+963955555555',
                'password' => Hash::make('password'),
                'role' => UserRole::Employee,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if ($loanOfficerRole) {
            $loanOfficer->roles()->sync([$loanOfficerRole->id]);
        }

        // Customer Service Agent user
        $cs = User::updateOrCreate(
            ['email' => 'cs@bank.com'],
            [
                'name' => 'Customer Service Agent',
                'phone' => '+963966666666',
                'password' => Hash::make('password'),
                'role' => UserRole::Employee,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if ($csRole) {
            $cs->roles()->sync([$csRole->id]);
        }

        // Auditor user
        $auditor = User::updateOrCreate(
            ['email' => 'auditor@bank.com'],
            [
                'name' => 'Compliance Auditor',
                'phone' => '+963977777777',
                'password' => Hash::make('password'),
                'role' => UserRole::Employee,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if ($auditorRole) {
            $auditor->roles()->sync([$auditorRole->id]);
        }

        // General Employee user (attached to Teller role)
        $employee = User::updateOrCreate(
            ['email' => 'employee@bank.com'],
            [
                'name' => 'Bank Employee',
                'phone' => '+963988888888',
                'password' => Hash::make('password'),
                'role' => UserRole::Employee,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if ($tellerRole) {
            $employee->roles()->sync([$tellerRole->id]);
        }

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

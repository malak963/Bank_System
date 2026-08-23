<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('phone', 20)
                    ->nullable()
                    ->unique()
                    ->after('email');
            });
        }

        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->enum('role', [
                    'admin',
                    'manager',
                    'employee',
                    'customer',
                ])->default('customer')->after('password');
            });
        }

        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->boolean('is_active')
                    ->default(true)
                    ->after('role');
            });
        }

        if (! Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->timestamp('last_login_at')
                    ->nullable()
                    ->after('email_verified_at');
            });
        }

        if (! Schema::hasIndex('users', ['role', 'is_active'])) {
            Schema::table('users', function (Blueprint $table): void {
                $table->index(['role', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('users', ['role', 'is_active'])) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropIndex(['role', 'is_active']);
            });
        }

        if (Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropUnique(['phone']);
                $table->dropColumn('phone');
            });
        }

        if (Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('last_login_at');
            });
        }

        if (Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('role');
            });
        }
    }
};

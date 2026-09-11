<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_types', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->string('description', 500)->nullable();
            $table->char('currency', 3)->default('SYP');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        DB::table('account_types')->insert([
            ['code' => 'CURRENT', 'name' => 'Current Account', 'description' => 'Everyday banking account.', 'currency' => 'SYP', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'SAVINGS', 'name' => 'Savings Account', 'description' => 'Savings account for personal deposits.', 'currency' => 'SYP', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'USD-CURRENT', 'name' => 'USD Current Account', 'description' => 'Current account held in US dollars.', 'currency' => 'USD', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('account_types');
    }
};

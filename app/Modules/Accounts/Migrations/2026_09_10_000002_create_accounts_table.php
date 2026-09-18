<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('account_type_id')->constrained('account_types')->restrictOnDelete();
            $table->string('account_number', 20)->unique();
            $table->string('iban', 34)->unique();
            $table->enum('status', ['open', 'frozen', 'closed'])->default('open');
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->dateTime('opened_at')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->dateTime('frozen_at')->nullable();
            $table->string('freeze_reason', 500)->nullable();
            $table->timestamps();
            $table->index(['status', 'account_type_id']);
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

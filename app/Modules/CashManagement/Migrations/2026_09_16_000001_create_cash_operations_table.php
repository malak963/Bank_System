<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_operations', function (Blueprint $table): void {
            $table->id();
            $table->string('operation_reference', 32)->unique();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('teller_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->enum('operation_type', ['deposit', 'withdrawal', 'transfer', 'replenishment', 'withdrawal_to_vault', 'deposit_from_vault']);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('SYP');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('counterparty_name')->nullable();
            $table->string('counterparty_id')->nullable();
            $table->dateTime('operation_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['branch_id', 'status']);
            $table->index(['operation_type', 'status']);
            $table->index('operation_date');
            $table->index('teller_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_operations');
    }
};
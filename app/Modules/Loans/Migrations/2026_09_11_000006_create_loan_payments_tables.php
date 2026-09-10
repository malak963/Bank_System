<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->restrictOnDelete();
            $table->string('payment_reference', 40)->unique();
            $table->decimal('amount', 15, 2);
            $table->decimal('principal_amount', 15, 2)->default(0);
            $table->decimal('interest_amount', 15, 2)->default(0);
            $table->enum('payment_method', ['account_debit', 'cash', 'bank_transfer'])->default('account_debit');
            $table->dateTime('paid_at');
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notes', 500)->nullable();
            $table->timestamps();
            $table->index(['loan_id', 'paid_at']);
        });

        Schema::create('loan_payment_allocations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('loan_payment_id')->constrained('loan_payments')->cascadeOnDelete();
            $table->foreignId('installment_id')->constrained('installments')->restrictOnDelete();
            $table->decimal('amount', 15, 2);
            $table->decimal('principal_amount', 15, 2)->default(0);
            $table->decimal('interest_amount', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['loan_payment_id', 'installment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_payment_allocations');
        Schema::dropIfExists('loan_payments');
    }
};

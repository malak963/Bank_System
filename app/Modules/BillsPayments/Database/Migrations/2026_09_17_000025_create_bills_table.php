<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table): void {
            $table->id();
            $table->string('bill_reference', 40)->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->enum('bill_type', [
                'electricity',
                'water',
                'gas',
                'internet',
                'phone',
                'television',
                'insurance',
                'tax',
                'loan_installment',
                'subscription',
                'other',
            ])->default('other');
            $table->string('provider_name');
            $table->string('provider_account_number')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('SYP');
            $table->date('due_date');
            $table->enum('status', [
                'pending',
                'scheduled',
                'processing',
                'completed',
                'failed',
                'cancelled',
                'refunded',
            ])->default('pending');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failed_reason')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['customer_id', 'status']);
            $table->index(['account_id', 'status']);
            $table->index(['due_date', 'status']);
            $table->index('bill_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};

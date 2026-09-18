<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table): void {
            $table->id();
            $table->string('loan_reference', 32)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('loan_type_id')->constrained('loan_types')->restrictOnDelete();
            $table->decimal('requested_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();
            $table->decimal('disbursed_amount', 15, 2)->nullable();
            $table->decimal('annual_interest_rate', 5, 2);
            $table->unsignedSmallInteger('term_months');
            $table->enum('interest_method', ['reducing_balance', 'flat_rate']);
            $table->enum('repayment_frequency', ['monthly', 'quarterly']);
            $table->string('purpose', 500)->nullable();
            $table->enum('status', ['pending', 'under_review', 'approved', 'rejected', 'disbursed', 'active', 'paid_off', 'defaulted', 'cancelled'])->default('pending');
            $table->date('application_date');
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('rejected_at')->nullable();
            $table->string('rejection_reason', 1000)->nullable();
            $table->dateTime('disbursed_at')->nullable();
            $table->date('first_payment_date')->nullable();
            $table->decimal('total_interest', 15, 2)->default(0);
            $table->decimal('total_payable', 15, 2)->default(0);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('outstanding_principal', 15, 2)->default(0);
            $table->decimal('outstanding_interest', 15, 2)->default(0);
            $table->date('next_payment_date')->nullable();
            $table->timestamps();
            $table->index(['status', 'customer_id']);
            $table->index(['status', 'next_payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};

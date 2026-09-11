<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_types', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 24)->unique();
            $table->string('name', 120);
            $table->string('description', 500)->nullable();
            $table->char('currency', 3)->default('SYP');
            $table->decimal('minimum_amount', 15, 2);
            $table->decimal('maximum_amount', 15, 2);
            $table->unsignedSmallInteger('minimum_term_months');
            $table->unsignedSmallInteger('maximum_term_months');
            $table->decimal('annual_interest_rate', 5, 2);
            $table->enum('interest_method', ['reducing_balance', 'flat_rate'])->default('reducing_balance');
            $table->enum('repayment_frequency', ['monthly', 'quarterly'])->default('monthly');
            $table->boolean('requires_collateral')->default(false);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->index(['status', 'currency']);
        });

        DB::table('loan_types')->insert([
            ['code' => 'PERSONAL', 'name' => 'Personal Loan', 'description' => 'Flexible personal financing.', 'currency' => 'SYP', 'minimum_amount' => 100000, 'maximum_amount' => 50000000, 'minimum_term_months' => 6, 'maximum_term_months' => 60, 'annual_interest_rate' => 12.00, 'interest_method' => 'reducing_balance', 'repayment_frequency' => 'monthly', 'requires_collateral' => false, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'BUSINESS', 'name' => 'Business Loan', 'description' => 'Working capital financing for businesses.', 'currency' => 'SYP', 'minimum_amount' => 1000000, 'maximum_amount' => 250000000, 'minimum_term_months' => 12, 'maximum_term_months' => 84, 'annual_interest_rate' => 10.50, 'interest_method' => 'reducing_balance', 'repayment_frequency' => 'monthly', 'requires_collateral' => true, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_types');
    }
};

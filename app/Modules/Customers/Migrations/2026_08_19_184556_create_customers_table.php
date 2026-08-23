<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->restrictOnDelete();

            $table->string('customer_number')
                ->unique();

            $table->string('first_name', 100);

            $table->string('last_name', 100);

            $table->date('date_of_birth')
                ->nullable();

            $table->string('national_id', 64)
                ->unique();

            $table->string('phone_number', 32)
                ->nullable();

            $table->string('address')
                ->nullable();

            $table->enum('status', [
                'prospect',
                'active',
                'suspended',
                'closed',
            ])->default('prospect');

            $table->enum('kyc_status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->enum('risk_level', [
                'low',
                'medium',
                'high',
            ])->default('low');

            $table->index(['kyc_status', 'risk_level']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

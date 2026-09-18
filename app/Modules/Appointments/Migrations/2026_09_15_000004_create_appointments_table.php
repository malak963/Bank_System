<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->enum('service_type', ['general', 'loan_consultation', 'account_opening', 'wealth_management', 'card_services', 'complaint_resolution'])->default('general');
            $table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->date('appointment_date');
            $table->dateTime('appointment_time');
            $table->unsignedSmallInteger('estimated_duration')->default(15);
            $table->text('notes')->nullable();
            $table->dateTime('confirmation_sent_at')->nullable();
            $table->dateTime('reminder_sent_at')->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancellation_reason', 500)->nullable();
            $table->timestamps();
            
            $table->index(['branch_id', 'appointment_date', 'appointment_time']);
            $table->index(['customer_id', 'appointment_date']);
            $table->index(['status', 'appointment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

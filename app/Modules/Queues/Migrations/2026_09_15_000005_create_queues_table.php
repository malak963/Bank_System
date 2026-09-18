<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->restrictOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->string('ticket_number', 10);
            $table->enum('service_type', ['general', 'loan_consultation', 'account_opening', 'wealth_management', 'card_services', 'complaint_resolution'])->default('general');
            $table->enum('status', ['waiting', 'called', 'serving', 'completed', 'cancelled', 'no_show'])->default('waiting');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->dateTime('joined_at');
            $table->dateTime('called_at')->nullable();
            $table->dateTime('serving_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->unsignedSmallInteger('estimated_wait_time')->nullable();
            $table->unsignedSmallInteger('actual_wait_time')->nullable();
            $table->string('service_counter', 10)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['branch_id', 'ticket_number']);
            $table->index(['branch_id', 'status', 'joined_at']);
            $table->index(['customer_id', 'joined_at']);
            $table->index(['status', 'priority', 'joined_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};

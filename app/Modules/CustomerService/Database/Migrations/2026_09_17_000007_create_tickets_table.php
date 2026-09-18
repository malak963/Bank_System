<?php

use App\Modules\CustomerService\Enums\TicketCategory;
use App\Modules\CustomerService\Enums\TicketPriority;
use App\Modules\CustomerService\Enums\TicketStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('category', array_column(TicketCategory::cases(), 'value'));
            $table->enum('priority', array_column(TicketPriority::cases(), 'value'))->default(TicketPriority::Normal->value);
            $table->enum('status', array_column(TicketStatus::cases(), 'value'))->default(TicketStatus::Open->value);
            $table->string('subject');
            $table->text('description');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('escalated_at')->nullable();
            $table->foreignId('escalated_to')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('customer_satisfaction')->nullable();
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index(['status', 'priority']);
            $table->index(['category']);
            $table->index(['ticket_number']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

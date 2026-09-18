<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statements', function (Blueprint $table): void {
            $table->id();
            $table->string('statement_reference', 40)->unique();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->default(0);
            $table->decimal('total_debits', 15, 2)->default(0);
            $table->decimal('total_credits', 15, 2)->default(0);
            $table->integer('transaction_count')->default(0);
            $table->string('currency', 3)->default('SYP');
            $table->enum('status', ['pending', 'generating', 'completed', 'failed'])->default('pending');
            $table->timestamp('generated_at')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('file_size')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['account_id', 'period_start', 'period_end']);
            $table->index(['customer_id', 'period_start']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statements');
    }
};

<?php

use App\Modules\Transfers\Enums\TransferStatus;
use App\Modules\Transfers\Enums\TransferType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_reference')->unique();
            $table->foreignId('from_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('to_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->enum('transfer_type', array_column(TransferType::cases(), 'value'));
            $table->enum('status', array_column(TransferStatus::cases(), 'value'))->default(TransferStatus::Pending->value);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 10, 6)->nullable();
            $table->decimal('converted_amount', 15, 2)->nullable();
            $table->decimal('fees', 15, 2)->default(0);
            $table->decimal('total_deducted', 15, 2)->default(0);
            $table->string('recipient_name')->nullable();
            $table->string('recipient_account')->nullable();
            $table->string('recipient_bank')->nullable();
            $table->text('recipient_bank_address')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('iban')->nullable();
            $table->string('routing_number')->nullable();
            $table->string('reference')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['from_account_id', 'status']);
            $table->index(['to_account_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['transfer_type', 'status']);
            $table->index(['status', 'scheduled_for']);
            $table->index(['transfer_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};

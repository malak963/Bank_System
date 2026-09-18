<?php

use App\Modules\Cards\Enums\CardBrand;
use App\Modules\Cards\Enums\CardStatus;
use App\Modules\Cards\Enums\CardType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_number', 16)->unique();
            $table->string('card_holder_name');
            $table->enum('card_type', array_column(CardType::cases(), 'value'));
            $table->enum('card_brand', array_column(CardBrand::cases(), 'value'));
            $table->integer('expiry_month');
            $table->integer('expiry_year');
            $table->string('cvv', 4);
            $table->string('pin')->nullable();
            $table->enum('status', array_column(CardStatus::cases(), 'value'))->default(CardStatus::Pending->value);
            $table->foreignId('account_id')->constrained()->restrictOnDelete();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->decimal('daily_limit', 15, 2)->default(5000);
            $table->decimal('monthly_limit', 15, 2)->default(20000);
            $table->boolean('international_enabled')->default(false);
            $table->boolean('online_enabled')->default(true);
            $table->boolean('contactless_enabled')->default(true);
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->text('block_reason')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->text('replacement_reason')->nullable();
            $table->foreignId('replaced_by_card_id')->nullable()->constrained('cards')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['card_number']);
            $table->index(['expires_at']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};

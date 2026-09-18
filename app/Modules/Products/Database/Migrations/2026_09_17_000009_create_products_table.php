<?php

use App\Modules\Products\Enums\ProductStatus;
use App\Modules\Products\Enums\ProductType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('product_type', array_column(ProductType::cases(), 'value'));
            $table->enum('status', array_column(ProductStatus::cases(), 'value'))->default(ProductStatus::Active->value);
            $table->string('name');
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->integer('term_months')->nullable();
            $table->timestamp('maturity_date')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->decimal('min_balance', 15, 2)->default(0);
            $table->decimal('max_balance', 15, 2)->nullable();
            $table->decimal('interest_earned', 15, 2)->default(0);
            $table->timestamp('last_interest_calculation')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['account_id']);
            $table->index(['branch_id']);
            $table->index(['product_type', 'status']);
            $table->index(['maturity_date']);
            $table->index(['product_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

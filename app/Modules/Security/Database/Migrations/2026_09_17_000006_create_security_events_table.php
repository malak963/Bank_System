<?php

use App\Modules\Security\Enums\SecurityEventType;
use App\Modules\Security\Enums\SecurityLevel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->enum('event_type', array_column(SecurityEventType::cases(), 'value'));
            $table->enum('security_level', array_column(SecurityLevel::cases(), 'value'))->default(SecurityLevel::Medium->value);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_id')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->json('details')->nullable();
            $table->string('source')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('resolution_notes')->nullable();
            $table->string('action_taken')->nullable();
            $table->boolean('blocked')->default(false);
            $table->timestamp('blocked_until')->nullable();
            $table->json('metadata')->nullable();
            $table->morphs('related_entity');
            $table->timestamps();

            $table->index(['event_type', 'security_level']);
            $table->index(['user_id', 'created_at']);
            $table->index(['customer_id', 'created_at']);
            $table->index(['is_resolved']);
            $table->index(['blocked']);
            $table->index(['created_at']);
            $table->index(['ip_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};

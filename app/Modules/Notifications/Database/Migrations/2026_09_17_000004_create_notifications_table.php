<?php

use App\Modules\Notifications\Enums\NotificationChannel;
use App\Modules\Notifications\Enums\NotificationStatus;
use App\Modules\Notifications\Enums\NotificationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('notification_type', array_column(NotificationType::cases(), 'value'));
            $table->enum('channel', array_column(NotificationChannel::cases(), 'value'));
            $table->enum('status', array_column(NotificationStatus::cases(), 'value'))->default(NotificationStatus::Pending->value);
            $table->string('subject')->nullable();
            $table->text('message');
            $table->json('data')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->integer('retry_count')->default(0);
            $table->integer('priority')->default(5);
            $table->json('metadata')->nullable();
            $table->morphs('notifiable');
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['status', 'scheduled_at']);
            $table->index(['notification_type', 'channel']);
            $table->index(['priority']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

<?php

use App\Modules\Reports\Enums\ReportFormat;
use App\Modules\Reports\Enums\ReportStatus;
use App\Modules\Reports\Enums\ReportType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->enum('report_type', array_column(ReportType::cases(), 'value'));
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', array_column(ReportStatus::cases(), 'value'))->default(ReportStatus::Pending->value);
            $table->enum('format', array_column(ReportFormat::cases(), 'value'))->default(ReportFormat::PDF->value);
            $table->json('parameters')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_path')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->integer('record_count')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['report_type', 'status']);
            $table->index(['generated_by']);
            $table->index(['branch_id']);
            $table->index(['status', 'scheduled_at']);
            $table->index(['expires_at']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};

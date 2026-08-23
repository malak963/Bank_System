<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->enum('identity_document_type', [
                'national_id',
                'passport',
                'driver_license',
                'residence_permit',
            ])->nullable()->after('national_id');

            $table->string('identity_document_number', 80)
                ->nullable()
                ->after('identity_document_type');

            $table->char('identity_document_country', 2)
                ->nullable()
                ->after('identity_document_number');

            $table->date('identity_document_expires_at')
                ->nullable()
                ->after('identity_document_country');

            $table->string('kyc_reference', 64)
                ->nullable()
                ->unique()
                ->after('kyc_status');

            $table->foreignId('kyc_reviewed_by')
                ->nullable()
                ->after('kyc_reference')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('kyc_reviewed_at')
                ->nullable()
                ->after('kyc_reviewed_by');

            $table->text('kyc_rejection_reason')
                ->nullable()
                ->after('kyc_reviewed_at');

            $table->index(['identity_document_type', 'identity_document_country'], 'customers_identity_document_lookup_index');
            $table->index(['kyc_status', 'kyc_reviewed_at'], 'customers_kyc_review_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->dropIndex('customers_identity_document_lookup_index');
            $table->dropIndex('customers_kyc_review_lookup_index');
            $table->dropConstrainedForeignId('kyc_reviewed_by');
            $table->dropUnique(['kyc_reference']);
            $table->dropColumn([
                'identity_document_type',
                'identity_document_number',
                'identity_document_country',
                'identity_document_expires_at',
                'kyc_reference',
                'kyc_reviewed_at',
                'kyc_rejection_reason',
            ]);
        });
    }
};

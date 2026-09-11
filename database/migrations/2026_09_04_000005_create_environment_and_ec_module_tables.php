<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Environment Projects Table (B1 & B2)
        Schema::create('environment_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code', 50)->unique()->comment('e.g. ENV-B2-2026-0001');
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('mining_application_id')->nullable()->constrained('mining_applications')->nullOnDelete();
            $table->foreignId('lease_application_id')->nullable()->constrained('lease_applications')->nullOnDelete();
            $table->enum('category', ['B1', 'B2'])->default('B2');
            $table->string('project_name');
            $table->foreignId('district_id')->constrained('districts')->restrictOnDelete();
            $table->string('location')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email')->nullable();
            $table->date('public_hearing_date')->nullable()->comment('B1 category public hearing date');
            $table->string('public_hearing_minutes_file')->nullable();
            $table->enum('status', ['draft', 'validation', 'approved', 'reported', 'archived'])->default('draft');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['district_id', 'category', 'status'], 'idx_env_cat_status');
            $table->index(['customer_id', 'status']);
        });

        // 2. Environment Documents Table
        Schema::create('environment_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('environment_project_id')->constrained('environment_projects')->cascadeOnDelete();
            $table->foreignId('folder_id')->constrained('folders')->cascadeOnDelete();
            $table->foreignId('document_field_id')->nullable()->constrained('document_fields')->nullOnDelete();
            $table->string('document_name');
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type', 20)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->enum('status', ['pending', 'uploaded', 'validated', 'approved', 'revision_required'])->default('pending');
            $table->text('review_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['environment_project_id', 'folder_id', 'status'], 'idx_env_doc_status');
        });

        // 3. EC Certificates Table
        Schema::create('ec_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('ec_ref_no', 100)->unique()->comment('Environmental Clearance Certificate Reference No');
            $table->foreignId('environment_project_id')->constrained('environment_projects')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('lease_application_id')->nullable()->constrained('lease_applications')->nullOnDelete();
            $table->string('parivesh_app_no', 100)->nullable();
            $table->string('applicant_name');
            $table->date('issue_date');
            $table->date('expiry_date');
            $table->integer('validity_years')->nullable();
            $table->enum('communication_type', ['Grant', 'Rejection', 'ToR'])->default('Grant');
            $table->string('certificate_file')->nullable()->comment('Certificate PDF file path');
            $table->text('conditions_summary')->nullable();
            $table->enum('status', ['active', 'expired', 'surrendered', 'revoked'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('ec_ref_no');
            $table->index('expiry_date'); // for 90/60/30 day expiry alerts
            $table->index(['customer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ec_certificates');
        Schema::dropIfExists('environment_documents');
        Schema::dropIfExists('environment_projects');
    }
};

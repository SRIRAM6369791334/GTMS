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
        // 1. Lease Applications Table
        Schema::create('lease_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_no', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('district_id')->constrained('districts')->restrictOnDelete();
            $table->foreignId('category_id')->constrained('lease_categories')->restrictOnDelete();
            $table->foreignId('mineral_id')->constrained('minerals')->restrictOnDelete();
            $table->string('taluk')->nullable();
            $table->string('village')->nullable();
            $table->decimal('area_extent_ha', 10, 2)->nullable()->comment('Area in Hectares');
            $table->string('area_extent_acres', 100)->nullable()->comment('Area in Acres & Cents');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('lease_period_years')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_mobile', 15)->nullable();
            $table->tinyInteger('current_step')->default(1)->comment('Wizard Step 1 to 7');
            $table->enum('status', ['draft', 'submitted', 'under_scrutiny', 'approved', 'rejected', 'expired'])->default('draft');
            $table->string('go_number', 100)->nullable()->comment('Government Order Number');
            $table->date('go_date')->nullable();
            $table->string('go_file')->nullable()->comment('Order PDF copy');
            $table->text('rejection_note')->nullable();
            $table->foreignId('assigned_inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // High-concurrency performance indexes
            $table->index(['district_id', 'status']);
            $table->index(['customer_id', 'status']);
            $table->index(['branch_id', 'status']);
            $table->index('end_date'); // for 90/60/30 day renewal expiry alerts
        });

        // 2. Lease Survey Numbers Table (1:N)
        Schema::create('lease_survey_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_application_id')->constrained('lease_applications')->cascadeOnDelete();
            $table->string('survey_no', 100);
            $table->decimal('extent_ha', 10, 4)->nullable();
            $table->string('classification', 100)->nullable()->comment('Dry, Wet, Poramboke, etc.');
            $table->string('pattadar_name')->nullable();
            $table->timestamps();

            $table->index('survey_no');
        });

        // 3. MIMAS Portal Credentials Table (1:N)
        Schema::create('mimas_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_application_id')->constrained('lease_applications')->cascadeOnDelete();
            $table->string('user_id');
            $table->text('password')->comment('Encrypted AES-256 via Laravel Crypt');
            $table->string('email')->nullable();
            $table->string('contact_number', 15)->nullable();
            $table->string('mimas_ack_no', 100)->nullable();
            $table->date('ack_date')->nullable();
            $table->string('portal_status', 50)->default('pending');
            $table->timestamps();
        });

        // 4. Lease Documents Table
        Schema::create('lease_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_application_id')->constrained('lease_applications')->cascadeOnDelete();
            $table->foreignId('folder_id')->constrained('folders')->cascadeOnDelete();
            $table->foreignId('document_field_id')->nullable()->constrained('document_fields')->nullOnDelete();
            $table->string('document_name');
            $table->string('file_name')->nullable()->comment('Original file name');
            $table->string('file_path')->nullable()->comment('Disk storage path');
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

            $table->index(['lease_application_id', 'folder_id', 'status'], 'idx_lease_doc_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_documents');
        Schema::dropIfExists('mimas_credentials');
        Schema::dropIfExists('lease_survey_numbers');
        Schema::dropIfExists('lease_applications');
    }
};

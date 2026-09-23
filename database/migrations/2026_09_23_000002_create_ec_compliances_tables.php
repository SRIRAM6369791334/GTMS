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
        // 1. EC Half-Yearly Compliance Table
        Schema::create('ec_compliances', function (Blueprint $table) {
            $table->id();
            $table->string('compliance_no', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('environment_project_id')->nullable()->constrained('environment_projects')->nullOnDelete();
            $table->foreignId('ec_certificate_id')->nullable()->constrained('ec_certificates')->nullOnDelete();
            $table->string('project_name');
            $table->foreignId('district_id')->constrained('districts')->restrictOnDelete();
            $table->string('taluk_village')->nullable();
            $table->foreignId('mineral_id')->nullable()->constrained('minerals')->nullOnDelete();
            
            // Half-Yearly Compliance Period & Submission Details
            $table->string('compliance_period', 100)->comment('e.g. April 2026 - September 2026');
            $table->string('compliance_year', 10)->default('2026');
            $table->date('submission_due_date')->nullable();
            $table->date('submission_date')->nullable();
            
            // Parivesh (MoEFCC) Online Details
            $table->string('parivesh_app_no', 100)->nullable();
            $table->string('parivesh_acknowledgement_no', 100)->nullable();
            $table->date('parivesh_uploaded_date')->nullable();
            
            // Laboratory Details
            $table->string('nabl_lab_name')->nullable()->comment('NABL accredited testing lab');
            $table->string('nabl_certificate_no', 100)->nullable();
            $table->date('monitoring_date')->nullable();
            
            // Status & Ledger
            $table->enum('status', [
                'draft', 
                'documents_collected', 
                'lab_analysed', 
                'report_prepared', 
                'uploaded_to_parivesh', 
                'completed',
                'archived'
            ])->default('draft');

            $table->decimal('product_value', 12, 2)->default(0.00);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('pending_amount', 12, 2)->default(0.00);
            $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
            $table->text('payment_notes')->nullable();

            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'status']);
            $table->index(['district_id', 'status']);
            $table->index('compliance_period');
        });

        // 2. EC Compliance Documents Table (Categorized by the 4 diagram pillars)
        Schema::create('ec_compliance_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ec_compliance_id')->constrained('ec_compliances')->cascadeOnDelete();
            $table->enum('folder_category', [
                'documents',      // 1. Documents (19 items)
                'site_analysis',  // 2. Site Analysis Study (NABL 4 items)
                'report',         // 3. Report (3 items)
                'parivesh_upload' // 4. Uploading Report (Parivesh receipt)
            ])->default('documents');
            $table->string('document_name');
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type', 20)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_custom')->default(false);
            $table->enum('status', ['pending', 'uploaded', 'verified', 'rejected'])->default('pending');
            $table->text('review_note')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ec_compliance_id', 'folder_category'], 'idx_ecc_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ec_compliance_documents');
        Schema::dropIfExists('ec_compliances');
    }
};

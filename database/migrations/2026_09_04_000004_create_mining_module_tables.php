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
        // 1. Mining Applications Table
        Schema::create('mining_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_no', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('lease_application_id')->nullable()->constrained('lease_applications')->nullOnDelete();
            $table->foreignId('parent_plan_id')->nullable()->constrained('mining_applications')->nullOnDelete()->comment('Self-referencing for Revised/Modified plans');
            $table->foreignId('applicant_type_id')->nullable()->constrained('applicant_types')->nullOnDelete();
            $table->foreignId('district_id')->constrained('districts')->restrictOnDelete();
            $table->foreignId('mineral_id')->constrained('minerals')->restrictOnDelete();
            $table->foreignId('plan_type_id')->constrained('plan_types')->restrictOnDelete();
            $table->string('taluk')->nullable();
            $table->string('village')->nullable();
            $table->text('survey_numbers_text')->nullable();
            $table->decimal('area_extent_ha', 10, 2)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('validity_years')->default(5);
            $table->enum('stage', ['6.1', '6.2', '6.3', '6.4', '6.5', '6.6'])->default('6.1');
            $table->enum('status', ['draft', 'scrutiny', 'inspection', 'presentation', 'approved', 'rejected', 'archived'])->default('draft');
            $table->string('rqp_name')->nullable()->comment('Recognized Qualified Person');
            $table->string('rqp_reg_no', 100)->nullable();
            $table->decimal('safety_distance_meters', 8, 2)->nullable();
            $table->foreignId('assigned_inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('approval_order_no', 100)->nullable();
            $table->date('approval_date')->nullable();
            $table->string('approval_file')->nullable();
            $table->string('kml_file_path')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['district_id', 'stage', 'status'], 'idx_mining_stage_status');
            $table->index(['customer_id', 'status']);
            $table->index(['branch_id', 'status']);
            $table->index('end_date');
        });

        // 2. Mining Boundary Points Table (1:N)
        Schema::create('mining_boundary_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mining_application_id')->constrained('mining_applications')->cascadeOnDelete();
            $table->string('pillar_id', 50)->comment('P1, P2, P3...');
            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('elevation', 8, 2)->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->index(['mining_application_id', 'pillar_id']);
        });

        // 3. Mining Production Schedules Table (1:N)
        Schema::create('mining_production_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mining_application_id')->constrained('mining_applications')->cascadeOnDelete();
            $table->tinyInteger('year_number')->comment('Year 1 to 5');
            $table->decimal('production_target', 12, 2)->comment('Production Target CBM/Tonnes');
            $table->decimal('waste_removal', 12, 2)->nullable();
            $table->timestamps();

            $table->unique(['mining_application_id', 'year_number'], 'uq_mining_app_year');
        });

        // 4. Mining Documents Table
        Schema::create('mining_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mining_application_id')->constrained('mining_applications')->cascadeOnDelete();
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

            $table->index(['mining_application_id', 'folder_id', 'status'], 'idx_mining_doc_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mining_documents');
        Schema::dropIfExists('mining_production_schedules');
        Schema::dropIfExists('mining_boundary_points');
        Schema::dropIfExists('mining_applications');
    }
};

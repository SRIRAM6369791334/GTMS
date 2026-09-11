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
        // 1. PPT Applications Table
        Schema::create('ppt_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_no', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('environment_project_id')->nullable()->constrained('environment_projects')->nullOnDelete();
            $table->string('project_name');
            $table->foreignId('district_id')->constrained('districts')->restrictOnDelete();
            $table->string('taluk_village')->nullable();
            $table->foreignId('mineral_id')->constrained('minerals')->restrictOnDelete();
            $table->enum('status', ['draft', 'agenda_scheduled', 'presented', 'approved', 'rejected', 'archived'])->default('draft');
            $table->string('rqp_attending')->nullable();
            $table->string('company_rep_attending')->nullable();
            $table->string('rep_mobile', 15)->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['district_id', 'status']);
            $table->index(['customer_id', 'status']);
        });

        // 2. PPT Agendas Table (1:N)
        Schema::create('ppt_agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppt_application_id')->constrained('ppt_applications')->cascadeOnDelete();
            $table->enum('committee_type', ['SEAC', 'SEIAA'])->default('SEAC');
            $table->string('meeting_no', 50);
            $table->string('item_no', 50);
            $table->date('meeting_date');
            $table->string('agenda_pdf')->nullable();
            $table->string('mom_pdf')->nullable()->comment('Minutes of Meeting PDF');
            $table->enum('outcome', ['Recommended', 'Query_EDS', 'Query_ADS', 'Rejected'])->nullable();
            $table->timestamps();

            $table->index('meeting_date');
        });

        // 3. PPT Documents Table (11 Folders)
        Schema::create('ppt_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppt_application_id')->constrained('ppt_applications')->cascadeOnDelete();
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
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ppt_application_id', 'folder_id', 'status'], 'idx_ppt_doc_status');
        });

        // 4. DGPS Surveys Table
        Schema::create('dgps_surveys', function (Blueprint $table) {
            $table->id();
            $table->string('survey_no', 50)->unique();
            $table->string('field_book_no', 100)->nullable();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('lease_application_id')->nullable()->constrained('lease_applications')->nullOnDelete();
            $table->foreignId('mining_application_id')->nullable()->constrained('mining_applications')->nullOnDelete();
            $table->decimal('lease_area_ha', 10, 2)->nullable();
            $table->decimal('surveyed_area_ha', 10, 2)->nullable();
            $table->decimal('area_discrepancy_ha', 10, 2)->nullable();
            $table->string('location')->nullable();
            $table->date('survey_date')->nullable();
            $table->foreignId('surveyor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('survey_team_notes')->nullable();
            $table->string('instrument_model', 100)->nullable();
            $table->string('instrument_serial_no', 100)->nullable();
            $table->enum('survey_status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->enum('report_status', ['pending', 'verified', 'dispatched'])->default('pending');
            $table->string('gtm_report_file')->nullable();
            $table->string('autocad_dwg_file')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'survey_status']);
            $table->index('survey_date');
        });

        // 5. DGPS GCP Points Table (1:N)
        Schema::create('dgps_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dgps_survey_id')->constrained('dgps_surveys')->cascadeOnDelete();
            $table->string('pillar_no', 50);
            $table->decimal('latitude', 11, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('elevation', 8, 2)->nullable();
            $table->timestamps();

            $table->index(['dgps_survey_id', 'pillar_no']);
        });

        // 6. DGPS Documents Table
        Schema::create('dgps_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dgps_survey_id')->constrained('dgps_surveys')->cascadeOnDelete();
            $table->foreignId('folder_id')->constrained('folders')->cascadeOnDelete();
            $table->string('document_name');
            $table->string('file_path')->nullable();
            $table->string('status', 30)->default('pending');
            $table->timestamps();
        });

        // 7. Drone Surveys Table
        Schema::create('drone_surveys', function (Blueprint $table) {
            $table->id();
            $table->string('survey_no', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('lease_application_id')->nullable()->constrained('lease_applications')->nullOnDelete();
            $table->foreignId('mining_application_id')->nullable()->constrained('mining_applications')->nullOnDelete();
            $table->decimal('lease_area', 10, 2)->nullable();
            $table->string('location')->nullable();
            $table->date('flight_date')->nullable();
            $table->string('drone_pilot_name')->nullable();
            $table->string('pilot_rpc_no', 100)->nullable();
            $table->string('drone_uin_no', 100)->nullable();
            $table->string('drone_model', 100)->nullable();
            $table->decimal('altitude_meters', 8, 2)->nullable();
            $table->decimal('gsd_cm_px', 6, 2)->nullable();
            $table->decimal('extracted_volume_cbm', 14, 2)->nullable()->comment('Volumetric survey result');
            $table->enum('survey_status', ['scheduled', 'flying_completed', 'processing', 'deliverables_ready', 'report_signed'])->default('scheduled');
            $table->text('deliverable_files_path')->nullable();
            $table->string('gtms_report_file')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'survey_status']);
            $table->index('flight_date');
        });

        // 8. Drone Documents Table
        Schema::create('drone_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drone_survey_id')->constrained('drone_surveys')->cascadeOnDelete();
            $table->foreignId('folder_id')->constrained('folders')->cascadeOnDelete();
            $table->string('document_name');
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('status', 30)->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drone_documents');
        Schema::dropIfExists('drone_surveys');
        Schema::dropIfExists('dgps_documents');
        Schema::dropIfExists('dgps_points');
        Schema::dropIfExists('dgps_surveys');
        Schema::dropIfExists('ppt_documents');
        Schema::dropIfExists('ppt_agendas');
        Schema::dropIfExists('ppt_applications');
    }
};

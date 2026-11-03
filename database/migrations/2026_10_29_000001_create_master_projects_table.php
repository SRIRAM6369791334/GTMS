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
        if (!Schema::hasTable('master_projects')) {
            Schema::create('master_projects', function (Blueprint $table) {
                $table->id();
                $table->string('master_application_id', 50)->unique();
                $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
                $table->string('taluk')->nullable();
                $table->string('village')->nullable();
                $table->text('survey_numbers')->nullable();
                $table->string('area_extent_ha')->nullable();
                $table->string('mineral_name')->nullable();
                $table->foreignId('lease_application_id')->nullable()->constrained('lease_applications')->nullOnDelete();
                $table->foreignId('dgps_survey_id')->nullable()->constrained('dgps_surveys')->nullOnDelete();
                $table->foreignId('drone_survey_id')->nullable()->constrained('drone_surveys')->nullOnDelete();
                $table->foreignId('mining_application_id')->nullable()->constrained('mining_applications')->nullOnDelete();
                $table->foreignId('environment_project_id')->nullable()->constrained('environment_projects')->nullOnDelete();
                $table->foreignId('ppt_application_id')->nullable()->constrained('ppt_applications')->nullOnDelete();
                $table->string('current_stage')->default('lease');
                $table->string('pipeline_status')->default('in_progress');
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index('current_stage');
                $table->index('pipeline_status');
                $table->index(['current_stage', 'pipeline_status']);
                $table->index('branch_id');
                $table->index('customer_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_projects');
    }
};

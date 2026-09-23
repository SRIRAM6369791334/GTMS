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
        Schema::table('environment_projects', function (Blueprint $table) {
            $table->string('b1_stage', 50)->nullable()->default('sc1_prep')->after('sub_category');
            $table->unsignedBigInteger('ppt_stage_1_id')->nullable()->after('b1_stage');
            $table->unsignedBigInteger('ppt_stage_2_id')->nullable()->after('ppt_stage_1_id');
        });

        Schema::table('ppt_applications', function (Blueprint $table) {
            $table->string('presentation_stage', 50)->default('tor_presentation')->after('environment_project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ppt_applications', function (Blueprint $table) {
            $table->dropColumn('presentation_stage');
        });

        Schema::table('environment_projects', function (Blueprint $table) {
            $table->dropColumn(['b1_stage', 'ppt_stage_1_id', 'ppt_stage_2_id']);
        });
    }
};

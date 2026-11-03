<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('lease_applications')) {
            DB::statement("ALTER TABLE `lease_applications` MODIFY COLUMN `status` VARCHAR(100) NOT NULL DEFAULT 'draft'");
            if (!Schema::hasColumn('lease_applications', 'status_notes') && !Schema::hasColumn('lease_applications', 'rejection_note')) {
                Schema::table('lease_applications', function (Blueprint $table) {
                    $table->text('status_notes')->nullable()->after('status');
                });
            }
        }

        if (Schema::hasTable('dgps_surveys')) {
            DB::statement("ALTER TABLE `dgps_surveys` MODIFY COLUMN `survey_status` VARCHAR(100) NOT NULL DEFAULT 'scheduled'");
            if (!Schema::hasColumn('dgps_surveys', 'status_notes')) {
                Schema::table('dgps_surveys', function (Blueprint $table) {
                    $table->text('status_notes')->nullable()->after('survey_status');
                });
            }
        }

        if (Schema::hasTable('drone_surveys')) {
            DB::statement("ALTER TABLE `drone_surveys` MODIFY COLUMN `survey_status` VARCHAR(100) NOT NULL DEFAULT 'scheduled'");
            if (!Schema::hasColumn('drone_surveys', 'status_notes')) {
                Schema::table('drone_surveys', function (Blueprint $table) {
                    $table->text('status_notes')->nullable()->after('survey_status');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe down migration: retain VARCHAR to prevent data truncation
    }
};

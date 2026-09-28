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
        // 1. environment_projects: modify status column to VARCHAR(100) and add status_notes
        if (Schema::hasTable('environment_projects')) {
            DB::statement("ALTER TABLE `environment_projects` MODIFY COLUMN `status` VARCHAR(100) NOT NULL DEFAULT 'draft'");
            if (!Schema::hasColumn('environment_projects', 'status_notes')) {
                Schema::table('environment_projects', function (Blueprint $table) {
                    $table->text('status_notes')->nullable()->after('status');
                });
            }
        }

        // 2. ec_certificates: modify status column to VARCHAR(100) and add status_notes
        if (Schema::hasTable('ec_certificates')) {
            DB::statement("ALTER TABLE `ec_certificates` MODIFY COLUMN `status` VARCHAR(100) NOT NULL DEFAULT 'active'");
            if (!Schema::hasColumn('ec_certificates', 'status_notes')) {
                Schema::table('ec_certificates', function (Blueprint $table) {
                    $table->text('status_notes')->nullable()->after('status');
                });
            }
        }

        // 3. ec_compliances: modify status column to VARCHAR(100) and add status_notes
        if (Schema::hasTable('ec_compliances')) {
            DB::statement("ALTER TABLE `ec_compliances` MODIFY COLUMN `status` VARCHAR(100) NOT NULL DEFAULT 'draft'");
            if (!Schema::hasColumn('ec_compliances', 'status_notes')) {
                Schema::table('ec_compliances', function (Blueprint $table) {
                    $table->text('status_notes')->nullable()->after('status');
                });
            }
        }

        // 4. ppt_applications: modify status column to VARCHAR(100) and add status_notes
        if (Schema::hasTable('ppt_applications')) {
            DB::statement("ALTER TABLE `ppt_applications` MODIFY COLUMN `status` VARCHAR(100) NOT NULL DEFAULT 'draft'");
            if (!Schema::hasColumn('ppt_applications', 'status_notes')) {
                Schema::table('ppt_applications', function (Blueprint $table) {
                    $table->text('status_notes')->nullable()->after('status');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('ppt_applications') && Schema::hasColumn('ppt_applications', 'status_notes')) {
            Schema::table('ppt_applications', function (Blueprint $table) {
                $table->dropColumn('status_notes');
            });
        }
        if (Schema::hasTable('ec_compliances') && Schema::hasColumn('ec_compliances', 'status_notes')) {
            Schema::table('ec_compliances', function (Blueprint $table) {
                $table->dropColumn('status_notes');
            });
        }
        if (Schema::hasTable('ec_certificates') && Schema::hasColumn('ec_certificates', 'status_notes')) {
            Schema::table('ec_certificates', function (Blueprint $table) {
                $table->dropColumn('status_notes');
            });
        }
        if (Schema::hasTable('environment_projects') && Schema::hasColumn('environment_projects', 'status_notes')) {
            Schema::table('environment_projects', function (Blueprint $table) {
                $table->dropColumn('status_notes');
            });
        }
    }
};

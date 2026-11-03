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
        if (Schema::hasTable('mining_applications')) {
            // Modify status column to VARCHAR(100) with default 'draft'
            DB::statement("ALTER TABLE `mining_applications` MODIFY COLUMN `status` VARCHAR(100) NOT NULL DEFAULT 'draft'");

            // Add status_notes column if not present
            if (!Schema::hasColumn('mining_applications', 'status_notes')) {
                Schema::table('mining_applications', function (Blueprint $table) {
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
        if (Schema::hasTable('mining_applications')) {
            if (Schema::hasColumn('mining_applications', 'status_notes')) {
                Schema::table('mining_applications', function (Blueprint $table) {
                    $table->dropColumn('status_notes');
                });
            }

            // Note: Keep status as VARCHAR(100) or revert if all rows conform to original enum
            try {
                DB::statement("ALTER TABLE `mining_applications` MODIFY COLUMN `status` ENUM('draft', 'scrutiny', 'inspection', 'presentation', 'approved', 'rejected', 'archived') NOT NULL DEFAULT 'draft'");
            } catch (\Throwable $e) {
                // If rows contain values outside the enum, remain VARCHAR(100) safely
            }
        }
    }
};

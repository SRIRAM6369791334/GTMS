<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mining wizard drafts are auto-saved after Step 1, before the district is
 * chosen (Step 4). district_id was NOT NULL, so the autosave stored a fake
 * district (id 1) on every new draft. Allow NULL until the user picks one;
 * the final submit still requires it.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mining_applications') && Schema::hasColumn('mining_applications', 'district_id')) {
            DB::statement('ALTER TABLE `mining_applications` MODIFY `district_id` BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('mining_applications') && Schema::hasColumn('mining_applications', 'district_id')) {
            $fallback = DB::table('districts')->orderBy('id')->value('id');
            if ($fallback) {
                DB::table('mining_applications')->whereNull('district_id')->update(['district_id' => $fallback]);
            }
            DB::statement('ALTER TABLE `mining_applications` MODIFY `district_id` BIGINT UNSIGNED NOT NULL');
        }
    }
};

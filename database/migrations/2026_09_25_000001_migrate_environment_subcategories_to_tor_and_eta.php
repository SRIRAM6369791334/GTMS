<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Alter column from ENUM('SC1', 'SC2') to VARCHAR(50) to allow new subcategories
        DB::statement("ALTER TABLE `environment_projects` MODIFY COLUMN `sub_category` VARCHAR(50) NULL");

        // 2. Migrate existing records
        DB::table('environment_projects')
            ->where('sub_category', 'SC1')
            ->update(['sub_category' => 'TOR']);

        DB::table('environment_projects')
            ->where('sub_category', 'SC2')
            ->update(['sub_category' => 'ETA']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Revert records back to SC1 and SC2
        DB::table('environment_projects')
            ->where('sub_category', 'TOR')
            ->update(['sub_category' => 'SC1']);

        DB::table('environment_projects')
            ->where('sub_category', 'ETA')
            ->update(['sub_category' => 'SC2']);

        // 2. Revert column back to ENUM('SC1', 'SC2')
        DB::statement("ALTER TABLE `environment_projects` MODIFY COLUMN `sub_category` ENUM('SC1', 'SC2') NULL");
    }
};

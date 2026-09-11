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
        DB::statement("
            ALTER TABLE `lease_applications` 
            MODIFY COLUMN `status` ENUM('draft', 'submitted', 'under_scrutiny', 'validated', 'approved', 'rejected', 'revision_required', 'expired') 
            NOT NULL DEFAULT 'draft'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE `lease_applications` 
            MODIFY COLUMN `status` ENUM('draft', 'submitted', 'under_scrutiny', 'approved', 'rejected', 'expired') 
            NOT NULL DEFAULT 'draft'
        ");
    }
};

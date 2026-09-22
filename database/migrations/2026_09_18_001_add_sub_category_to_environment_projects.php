<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds sub_category column to environment_projects table.
     * B1 projects → 'SC1' or 'SC2'
     * B2 projects → NULL (no sub category)
     */
    public function up(): void
    {
        Schema::table('environment_projects', function (Blueprint $table) {
            $table->enum('sub_category', ['SC1', 'SC2'])->nullable()->after('category')
                ->comment('B1 Sub Category: SC1=Site & Mining Documentation, SC2=EIA & TNPCB Submission. NULL for B2.');
        });

        // Auto-assign existing B1 projects to SC1 (safest default)
        DB::table('environment_projects')
            ->where('category', 'B1')
            ->whereNull('sub_category')
            ->update(['sub_category' => 'SC1']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('environment_projects', function (Blueprint $table) {
            $table->dropColumn('sub_category');
        });
    }
};

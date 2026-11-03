<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Department tables requiring master_project_id and common_id
     */
    protected array $departmentTables = [
        'lease_applications',
        'dgps_surveys',
        'drone_surveys',
        'mining_applications',
        'environment_projects',
        'ppt_applications',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->departmentTables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($table) {
                    // 1. Add common_id if missing
                    if (!Schema::hasColumn($table, 'common_id')) {
                        $tableBlueprint->string('common_id', 50)->nullable()->index();
                    }

                    // 2. Add master_project_id foreign key if missing
                    if (!Schema::hasColumn($table, 'master_project_id')) {
                        $tableBlueprint->foreignId('master_project_id')
                            ->nullable()
                            ->constrained('master_projects')
                            ->nullOnDelete();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tables where common_id was newly added in this migration
        $newCommonIdTables = [
            'dgps_surveys',
            'drone_surveys',
            'environment_projects',
            'ppt_applications',
        ];

        foreach ($this->departmentTables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($table, $newCommonIdTables) {
                    if (Schema::hasColumn($table, 'master_project_id')) {
                        $tableBlueprint->dropConstrainedForeignId('master_project_id');
                    }

                    if (in_array($table, $newCommonIdTables) && Schema::hasColumn($table, 'common_id')) {
                        $tableBlueprint->dropColumn('common_id');
                    }
                });
            }
        }
    }
};

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
            if (!Schema::hasColumn('environment_projects', 'secondary_phone')) {
                $table->string('secondary_phone', 25)->nullable()->after('contact_phone');
            }
            if (!Schema::hasColumn('environment_projects', 'secondary_contact_person')) {
                $table->string('secondary_contact_person', 255)->nullable()->after('contact_name');
            }
        });

        Schema::table('ppt_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('ppt_applications', 'rep_secondary_mobile')) {
                $table->string('rep_secondary_mobile', 25)->nullable()->after('rep_mobile');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('environment_projects', function (Blueprint $table) {
            $table->dropColumn(['secondary_phone', 'secondary_contact_person']);
        });

        Schema::table('ppt_applications', function (Blueprint $table) {
            $table->dropColumn(['rep_secondary_mobile']);
        });
    }
};

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
        Schema::table('ec_compliances', function (Blueprint $table) {
            $table->string('environment_project_name')->nullable()->after('environment_project_id');
            $table->string('ec_certificate_file')->nullable()->after('ec_certificate_id');
            $table->string('ec_certificate_name')->nullable()->after('ec_certificate_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ec_compliances', function (Blueprint $table) {
            $table->dropColumn(['environment_project_name', 'ec_certificate_file', 'ec_certificate_name']);
        });
    }
};

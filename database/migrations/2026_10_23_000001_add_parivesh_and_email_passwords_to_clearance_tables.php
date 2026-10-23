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
        // 1. environment_projects
        Schema::table('environment_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('environment_projects', 'parivesh_password')) {
                $table->string('parivesh_password', 255)->nullable()->after('contact_email');
            }
            if (!Schema::hasColumn('environment_projects', 'email_password')) {
                $table->string('email_password', 255)->nullable()->after('parivesh_password');
            }
        });

        // 2. ppt_applications
        Schema::table('ppt_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('ppt_applications', 'parivesh_password')) {
                $table->string('parivesh_password', 255)->nullable()->after('mineral_id');
            }
            if (!Schema::hasColumn('ppt_applications', 'email_password')) {
                $table->string('email_password', 255)->nullable()->after('parivesh_password');
            }
        });

        // 3. ec_certificates
        Schema::table('ec_certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('ec_certificates', 'parivesh_password')) {
                $table->string('parivesh_password', 255)->nullable()->after('parivesh_app_no');
            }
            if (!Schema::hasColumn('ec_certificates', 'email_password')) {
                $table->string('email_password', 255)->nullable()->after('parivesh_password');
            }
        });

        // 4. customers (universal profile persistence)
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'parivesh_password')) {
                $table->string('parivesh_password', 255)->nullable()->after('email');
            }
            if (!Schema::hasColumn('customers', 'email_password')) {
                $table->string('email_password', 255)->nullable()->after('parivesh_password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('environment_projects', function (Blueprint $table) {
            $table->dropColumn(['parivesh_password', 'email_password']);
        });

        Schema::table('ppt_applications', function (Blueprint $table) {
            $table->dropColumn(['parivesh_password', 'email_password']);
        });

        Schema::table('ec_certificates', function (Blueprint $table) {
            $table->dropColumn(['parivesh_password', 'email_password']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['parivesh_password', 'email_password']);
        });
    }
};

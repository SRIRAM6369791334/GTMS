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
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'secondary_contact_person')) {
                $table->string('secondary_contact_person', 255)->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('customers', 'secondary_mobile_num')) {
                $table->string('secondary_mobile_num', 15)->nullable()->after('mobile_num');
            }
        });

        Schema::table('lease_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('lease_applications', 'secondary_contact_person')) {
                $table->string('secondary_contact_person', 255)->nullable()->after('contact_person');
            }
            if (!Schema::hasColumn('lease_applications', 'secondary_contact_mobile')) {
                $table->string('secondary_contact_mobile', 15)->nullable()->after('contact_mobile');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['secondary_contact_person', 'secondary_mobile_num']);
        });

        Schema::table('lease_applications', function (Blueprint $table) {
            $table->dropColumn(['secondary_contact_person', 'secondary_contact_mobile']);
        });
    }
};

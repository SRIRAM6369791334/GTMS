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
            $table->string('primary_contact_person', 255)->nullable()->after('customer_id');
            $table->string('primary_phone', 25)->nullable()->after('primary_contact_person');
            $table->string('secondary_contact_person', 255)->nullable()->after('primary_phone');
            $table->string('secondary_phone', 25)->nullable()->after('secondary_contact_person');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ec_compliances', function (Blueprint $table) {
            $table->dropColumn(['primary_contact_person', 'primary_phone', 'secondary_contact_person', 'secondary_phone']);
        });
    }
};

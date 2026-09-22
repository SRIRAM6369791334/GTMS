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
            if (!Schema::hasColumn('customers', 'mimas_number')) {
                $table->string('mimas_number', 100)->nullable()->after('mimas_no');
            }
            if (!Schema::hasColumn('customers', 'mimas_status')) {
                $table->string('mimas_status', 100)->nullable()->after('mimas_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'mimas_status')) {
                $table->dropColumn('mimas_status');
            }
            if (Schema::hasColumn('customers', 'mimas_number')) {
                $table->dropColumn('mimas_number');
            }
        });
    }
};

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
        Schema::table('mining_applications', function (Blueprint $table) {
            $table->string('other_nature_of_work_name', 255)->nullable()->after('nature_of_work_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mining_applications', function (Blueprint $table) {
            $table->dropColumn('other_nature_of_work_name');
        });
    }
};

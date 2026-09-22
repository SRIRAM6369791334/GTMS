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
            $table->foreignId('nature_of_work_id')->nullable()->after('id')->constrained('nature_of_works')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mining_applications', function (Blueprint $table) {
            $table->dropForeign(['nature_of_work_id']);
            $table->dropColumn('nature_of_work_id');
        });
    }
};

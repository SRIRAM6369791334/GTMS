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
            $table->unsignedTinyInteger('current_step')->default(1)->after('stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mining_applications', function (Blueprint $table) {
            $table->dropColumn('current_step');
        });
    }
};

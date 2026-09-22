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
            $table->foreignId('mineral_id')->nullable()->change();
            $table->foreignId('plan_type_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mining_applications', function (Blueprint $table) {
            $table->foreignId('mineral_id')->nullable(false)->change();
            $table->foreignId('plan_type_id')->nullable(false)->change();
        });
    }
};

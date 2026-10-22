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
            $table->string('area', 50)->nullable()->change();
        });

        Schema::table('lease_applications', function (Blueprint $table) {
            $table->string('area_extent_ha', 50)->nullable()->change();
        });

        Schema::table('mining_applications', function (Blueprint $table) {
            $table->string('area_extent_ha', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('area', 10, 2)->nullable()->change();
        });

        Schema::table('lease_applications', function (Blueprint $table) {
            $table->decimal('area_extent_ha', 10, 2)->nullable()->change();
        });

        Schema::table('mining_applications', function (Blueprint $table) {
            $table->decimal('area_extent_ha', 10, 2)->nullable()->change();
        });
    }
};

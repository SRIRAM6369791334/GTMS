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
        if (!Schema::hasColumn('lease_applications', 'other_mineral_name')) {
            Schema::table('lease_applications', function (Blueprint $table) {
                $table->string('other_mineral_name', 255)->nullable()->after('mineral_id');
            });
        }

        if (!Schema::hasTable('lease_application_minerals')) {
            Schema::create('lease_application_minerals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lease_application_id')->constrained('lease_applications')->cascadeOnDelete();
                $table->foreignId('mineral_id')->constrained('minerals')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['lease_application_id', 'mineral_id'], 'lease_app_min_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lease_application_minerals');
        if (Schema::hasColumn('lease_applications', 'other_mineral_name')) {
            Schema::table('lease_applications', function (Blueprint $table) {
                $table->dropColumn('other_mineral_name');
            });
        }
    }
};

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
        Schema::create('mining_application_minerals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mining_application_id')->constrained('mining_applications')->cascadeOnDelete();
            $table->foreignId('mineral_id')->constrained('minerals')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['mining_application_id', 'mineral_id'], 'mining_app_min_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mining_application_minerals');
    }
};

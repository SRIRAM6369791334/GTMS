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
        Schema::create('application_handlers', function (Blueprint $table) {
            $table->id();
            $table->string('application_type', 50)->nullable()->index(); // e.g. 'lease', 'mining', 'environment', 'ec', 'dgps', 'drone', 'ppt'
            $table->unsignedBigInteger('application_id')->nullable()->index();
            $table->nullableMorphs('handlerable'); // polymorphic support: handlerable_type, handlerable_id
            $table->string('name', 255);
            $table->string('role', 255); // manually typed role
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_handlers');
    }
};

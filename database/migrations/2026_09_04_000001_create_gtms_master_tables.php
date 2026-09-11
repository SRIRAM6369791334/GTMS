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
        // 1. Districts Master (38 TN Districts)
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('code', 10)->unique()->nullable();
            $table->string('state', 100)->default('Tamil Nadu');
            $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
            $table->timestamps();
        });

        // 2. Minerals Master
        Schema::create('minerals', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->enum('category', ['Major', 'Minor'])->default('Minor');
            $table->string('default_unit', 20)->default('CBM')->comment('CBM or Tonnes');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // 3. Lease Categories Master (TN Minor Mineral Concession Rules)
        Schema::create('lease_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('e.g. Rule 12, Rule 19(1), MDCC');
            $table->string('name', 255);
            $table->enum('land_type', ['Patta', 'Poramboke', 'Both'])->default('Patta');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // 4. Plan Types Master
        Schema::create('plan_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('Mining Plan, Revised, Modified, Scheme');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // 5. Applicant Types Master
        Schema::create('applicant_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->comment('Individual, Partnership, Pvt Ltd, Public Ltd, Trust');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // 6. Modules Master
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('lease, mining, environment, ppt, dgps, drone');
            $table->string('name', 100);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        // 7. Folders Master (Module-Keyed)
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('modules')->cascadeOnDelete();
            $table->string('name', 100);
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->unique(['module_id', 'name']);
            $table->index(['module_id', 'sort_order']);
        });

        // 8. Document Fields Checklist Master (Folder-Keyed)
        Schema::create('document_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->constrained('folders')->cascadeOnDelete();
            $table->string('name', 255);
            $table->boolean('required')->default(true);
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->index(['folder_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_fields');
        Schema::dropIfExists('folders');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('applicant_types');
        Schema::dropIfExists('plan_types');
        Schema::dropIfExists('lease_categories');
        Schema::dropIfExists('minerals');
        Schema::dropIfExists('districts');
    }
};

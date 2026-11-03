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
        if (!Schema::hasTable('pipeline_movements')) {
            Schema::create('pipeline_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('master_project_id')->constrained('master_projects')->cascadeOnDelete();
                $table->string('from_department', 50);
                $table->string('to_department', 50);
                $table->string('action_type', 50); // e.g. 'Forward_Transfer', 'Returned_For_Correction', 'Resubmitted_Fix'
                $table->string('priority', 20)->default('normal');
                $table->string('mistake_category', 100)->nullable();
                $table->text('reason')->nullable();
                $table->text('rectification_notes')->nullable();
                $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('officer_name', 150)->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['master_project_id', 'created_at']);
                $table->index('action_type');
                $table->index('priority');
                $table->index(['from_department', 'to_department']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_movements');
    }
};

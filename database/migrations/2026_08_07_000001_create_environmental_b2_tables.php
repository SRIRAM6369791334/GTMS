<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('environmental_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code')->unique();
            $table->string('client_name');
            $table->string('project_name');
            $table->string('location')->nullable();
            $table->string('district')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('sub_category')->default('B2');
            $table->string('status')->default('draft');
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });

        Schema::create('environmental_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('environmental_projects')->cascadeOnDelete();
            $table->string('folder');
            $table->string('document_name');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->string('status')->default('pending');
            $table->text('review_note')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'folder', 'document_name']);
        });

        Schema::create('environmental_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('environmental_projects')->cascadeOnDelete();
            $table->string('action');
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('environmental_activities');
        Schema::dropIfExists('environmental_documents');
        Schema::dropIfExists('environmental_projects');
    }
};

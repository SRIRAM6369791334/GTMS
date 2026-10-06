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
        Schema::create('chunked_uploads', function (Blueprint $table) {
            $table->id();
            $table->string('upload_token', 64)->unique();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('file_name');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->bigInteger('total_size');
            $table->integer('chunk_size');
            $table->integer('total_chunks');
            $table->integer('uploaded_chunks')->default(0);
            $table->string('target_module')->nullable()->index();
            $table->unsignedBigInteger('reference_id')->nullable()->index();
            $table->string('file_path')->nullable();
            $table->string('file_hash', 64)->nullable(); // Client SHA-256
            $table->string('checksum_sha256', 64)->nullable(); // Verified SHA-256
            $table->enum('status', [
                'pending',
                'uploading',
                'assembled',
                'processing',
                'completed',
                'failed',
                'cancelled'
            ])->default('pending')->index();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('chunked_upload_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chunked_upload_id')
                ->constrained('chunked_uploads')
                ->cascadeOnDelete();
            $table->integer('chunk_index');
            $table->integer('chunk_size');
            $table->string('chunk_path');
            $table->string('chunk_hash', 64)->nullable();
            $table->boolean('is_uploaded')->default(true);
            $table->timestamps();

            $table->unique(['chunked_upload_id', 'chunk_index'], 'chunked_parts_upload_index_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chunked_upload_parts');
        Schema::dropIfExists('chunked_uploads');
    }
};

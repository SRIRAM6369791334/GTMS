<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'lease_documents',
            'mining_documents',
            'environment_documents',
            'ppt_documents',
            'ec_compliance_documents',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'file_type')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('file_type', 255)->nullable()->change();
                });
            }
        }

        if (Schema::hasTable('chunked_uploads') && Schema::hasColumn('chunked_uploads', 'mime_type')) {
            Schema::table('chunked_uploads', function (Blueprint $table) {
                $table->string('mime_type', 255)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'lease_documents',
            'mining_documents',
            'environment_documents',
            'ppt_documents',
            'ec_compliance_documents',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'file_type')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('file_type', 20)->nullable()->change();
                });
            }
        }

        if (Schema::hasTable('chunked_uploads') && Schema::hasColumn('chunked_uploads', 'mime_type')) {
            Schema::table('chunked_uploads', function (Blueprint $table) {
                $table->string('mime_type', 100)->change();
            });
        }
    }
};

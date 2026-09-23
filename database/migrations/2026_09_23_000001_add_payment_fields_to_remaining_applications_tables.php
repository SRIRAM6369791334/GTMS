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
        $tables = [
            'environment_projects',
            'ec_certificates',
            'ppt_applications',
            'dgps_surveys',
            'drone_surveys',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'product_value')) {
                        $table->decimal('product_value', 12, 2)->nullable()->default(0.00);
                    }
                    if (!Schema::hasColumn($tableName, 'paid_amount')) {
                        $table->decimal('paid_amount', 12, 2)->nullable()->default(0.00);
                    }
                    if (!Schema::hasColumn($tableName, 'pending_amount')) {
                        $table->decimal('pending_amount', 12, 2)->nullable()->default(0.00);
                    }
                    if (!Schema::hasColumn($tableName, 'payment_status')) {
                        $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'environment_projects',
            'ec_certificates',
            'ppt_applications',
            'dgps_surveys',
            'drone_surveys',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $cols = array_filter(['product_value', 'paid_amount', 'pending_amount', 'payment_status'], function ($col) use ($tableName) {
                        return Schema::hasColumn($tableName, $col);
                    });
                    if (!empty($cols)) {
                        $table->dropColumn($cols);
                    }
                });
            }
        }
    }
};

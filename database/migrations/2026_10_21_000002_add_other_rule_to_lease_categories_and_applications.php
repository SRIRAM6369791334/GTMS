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
        if (Schema::hasTable('lease_categories') && !Schema::hasColumn('lease_categories', 'is_custom')) {
            Schema::table('lease_categories', function (Blueprint $table) {
                $table->boolean('is_custom')->default(false)->after('status');
            });
        }

        if (Schema::hasTable('lease_applications') && !Schema::hasColumn('lease_applications', 'other_category_rule')) {
            Schema::table('lease_applications', function (Blueprint $table) {
                $table->string('other_category_rule', 255)->nullable()->after('category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lease_categories') && Schema::hasColumn('lease_categories', 'is_custom')) {
            Schema::table('lease_categories', function (Blueprint $table) {
                $table->dropColumn('is_custom');
            });
        }

        if (Schema::hasTable('lease_applications') && Schema::hasColumn('lease_applications', 'other_category_rule')) {
            Schema::table('lease_applications', function (Blueprint $table) {
                $table->dropColumn('other_category_rule');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mining_applications', function (Blueprint $table) {
            $table->string('other_mineral_name', 255)->nullable()->after('mineral_id');
        });
    }

    public function down(): void
    {
        Schema::table('mining_applications', function (Blueprint $table) {
            $table->dropColumn('other_mineral_name');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('created_by')->constrained('branches')->nullOnDelete();
        });

        // Sync branch_id from creator user if available
        DB::statement("
            UPDATE customers c
            JOIN users u ON c.created_by = u.id
            SET c.branch_id = u.branch_id
            WHERE c.branch_id IS NULL AND u.branch_id IS NOT NULL
        ");

        // Fallback remaining customers to Head Office (ID: 1) or first available branch
        $defaultBranchId = DB::table('branches')->where('id', 1)->value('id') ?? DB::table('branches')->value('id');
        if ($defaultBranchId) {
            DB::table('customers')->whereNull('branch_id')->update(['branch_id' => $defaultBranchId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};

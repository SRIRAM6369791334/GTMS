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
            $table->string('mimas_no', 50)->nullable()->after('company_name');
        });

        // Seed unique default MIMAS numbers for existing customers
        $sampleMimas = [
            1 => 'TN-MMS-SLM-001',
            2 => 'TN-MMS-CBE-002',
            3 => 'TN-MMS-MDU-003',
            4 => 'TN-MMS-ERD-004',
            5 => 'TN-MMS-TRI-005',
        ];

        $customers = DB::table('customers')->get();
        foreach ($customers as $c) {
            $mimas = $sampleMimas[$c->id] ?? ('TN-MMS-CUST-' . str_pad($c->id, 4, '0', STR_PAD_LEFT));
            DB::table('customers')->where('id', $c->id)->update(['mimas_no' => $mimas]);
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->unique('mimas_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['mimas_no']);
            $table->dropColumn('mimas_no');
        });
    }
};

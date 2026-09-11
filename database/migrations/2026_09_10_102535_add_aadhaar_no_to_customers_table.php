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
            $table->string('aadhaar_no', 20)->nullable()->after('pan');
        });

        // Seed unique default Aadhaar numbers for existing customers
        $sampleAadhaar = [
            1 => '9876-5432-1001',
            2 => '9876-5432-1002',
            3 => '9876-5432-1003',
            4 => '9876-5432-1004',
            5 => '9876-5432-1005',
        ];

        $customers = DB::table('customers')->get();
        foreach ($customers as $c) {
            $aadhaar = $sampleAadhaar[$c->id] ?? ('9876-5432-' . str_pad($c->id, 4, '0', STR_PAD_LEFT));
            DB::table('customers')->where('id', $c->id)->update(['aadhaar_no' => $aadhaar]);
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->unique('aadhaar_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['aadhaar_no']);
            $table->dropColumn('aadhaar_no');
        });
    }
};

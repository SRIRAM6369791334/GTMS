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
        if (!Schema::hasTable('bank_accounts')) {
            Schema::create('bank_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('account_name');
                $table->string('account_number')->unique();
                $table->string('bank_name');
                $table->string('branch_name')->nullable();
                $table->string('ifsc_code', 20);
                $table->boolean('is_default')->default(false);
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('quotations', 'bank_account_id')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->unsignedBigInteger('bank_account_id')->nullable()->after('exclusions');
                $table->foreign('bank_account_id')->references('id')->on('bank_accounts')->nullOnDelete();
            });
        }

        // Seed initial default GTMS bank account if not present
        $existing = DB::table('bank_accounts')->where('account_number', '107302000001265')->first();
        if (!$existing) {
            $defaultId = DB::table('bank_accounts')->insertGetId([
                'account_name'   => 'Geo Technical Mining Solutions',
                'account_number' => '107302000001265',
                'bank_name'      => 'Indian Overseas Bank',
                'branch_name'    => 'Oddapatti, Dharmapuri',
                'ifsc_code'      => 'IOBA0001073',
                'is_default'     => true,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Link existing quotations to this default bank account
            DB::table('quotations')->whereNull('bank_account_id')->update([
                'bank_account_id' => $defaultId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('quotations', 'bank_account_id')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->dropForeign(['bank_account_id']);
                $table->dropColumn('bank_account_id');
            });
        }

        Schema::dropIfExists('bank_accounts');
    }
};

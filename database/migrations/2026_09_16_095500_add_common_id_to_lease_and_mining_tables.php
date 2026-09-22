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
        // 1. Add common_id to lease_applications
        if (!Schema::hasColumn('lease_applications', 'common_id')) {
            Schema::table('lease_applications', function (Blueprint $table) {
                $table->string('common_id', 50)->nullable()->after('id')->index();
            });
        }

        // 2. Add common_id to mining_applications
        if (!Schema::hasColumn('mining_applications', 'common_id')) {
            Schema::table('mining_applications', function (Blueprint $table) {
                $table->string('common_id', 50)->nullable()->after('id')->index();
            });
        }

        // 3. Backfill common_id for existing lease_applications
        $leases = DB::table('lease_applications')->get();
        foreach ($leases as $lease) {
            $commonId = null;
            if ($lease->application_no) {
                // e.g., LA-2026-0011 -> GTMS-2026-0011
                if (preg_match('/LA-(\d{4}-\d+)/', $lease->application_no, $matches)) {
                    $commonId = 'GTMS-' . $matches[1];
                } elseif (preg_match('/LA-DRAFT-(\d{4}-\d+)/', $lease->application_no, $matches)) {
                    $commonId = 'GTMS-DRAFT-' . $matches[1];
                } else {
                    $commonId = 'GTMS-' . date('Y', strtotime($lease->created_at ?? 'now')) . '-' . str_pad($lease->id, 4, '0', STR_PAD_LEFT);
                }
            } else {
                $commonId = 'GTMS-' . date('Y', strtotime($lease->created_at ?? 'now')) . '-' . str_pad($lease->id, 4, '0', STR_PAD_LEFT);
            }

            DB::table('lease_applications')
                ->where('id', $lease->id)
                ->update(['common_id' => $commonId]);
        }

        // 4. Backfill common_id for existing mining_applications
        $minings = DB::table('mining_applications')->get();
        foreach ($minings as $mining) {
            $commonId = null;
            if ($mining->lease_application_id) {
                $parentLease = DB::table('lease_applications')->where('id', $mining->lease_application_id)->first();
                if ($parentLease && $parentLease->common_id) {
                    $commonId = $parentLease->common_id;
                }
            }

            if (!$commonId) {
                if ($mining->application_no && preg_match('/(?:MDG|MP)-(\d{4}-\d+)/', $mining->application_no, $matches)) {
                    $commonId = 'GTMS-' . $matches[1];
                } else {
                    $commonId = 'GTMS-' . date('Y', strtotime($mining->created_at ?? 'now')) . '-' . str_pad($mining->id, 4, '0', STR_PAD_LEFT);
                }
            }

            DB::table('mining_applications')
                ->where('id', $mining->id)
                ->update(['common_id' => $commonId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('lease_applications', 'common_id')) {
            Schema::table('lease_applications', function (Blueprint $table) {
                $table->dropColumn('common_id');
            });
        }

        if (Schema::hasColumn('mining_applications', 'common_id')) {
            Schema::table('mining_applications', function (Blueprint $table) {
                $table->dropColumn('common_id');
            });
        }
    }
};

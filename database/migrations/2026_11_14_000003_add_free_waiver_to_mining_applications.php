<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * "Free" (fee waived) payment status for mining applications, same shape as
 * 2026_11_14_000001 for lease applications: a free application stores
 * product_value = paid_amount = pending_amount = 0 and keeps the quoted value
 * and the mandatory reason in waived_amount / waiver_reason.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('mining_applications')) {
            return;
        }

        DB::statement("ALTER TABLE `mining_applications` MODIFY `payment_status` ENUM('pending','partial','paid','free') NOT NULL DEFAULT 'pending'");

        Schema::table('mining_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('mining_applications', 'waived_amount')) {
                $table->decimal('waived_amount', 12, 2)->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('mining_applications', 'waiver_reason')) {
                $table->text('waiver_reason')->nullable()->after('waived_amount');
            }
            if (!Schema::hasColumn('mining_applications', 'waived_by')) {
                $table->unsignedBigInteger('waived_by')->nullable()->after('waiver_reason');
            }
            if (!Schema::hasColumn('mining_applications', 'waived_at')) {
                $table->timestamp('waived_at')->nullable()->after('waived_by');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('mining_applications')) {
            return;
        }

        Schema::table('mining_applications', function (Blueprint $table) {
            foreach (['waived_at', 'waived_by', 'waiver_reason', 'waived_amount'] as $col) {
                if (Schema::hasColumn('mining_applications', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        // Free rows fall back to "paid" (nothing due) before the value is removed
        DB::table('mining_applications')->where('payment_status', 'free')->update(['payment_status' => 'paid']);
        DB::statement("ALTER TABLE `mining_applications` MODIFY `payment_status` ENUM('pending','partial','paid') NOT NULL DEFAULT 'pending'");
    }
};

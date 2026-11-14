<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * "Free" (fee waived) payment status for lease applications.
 *
 * A free application is stored with a chargeable value of 0 (product_value,
 * paid_amount and pending_amount = 0), so every existing calculation in
 * Accounts, ledgers, receipts and reports naturally shows nothing due.
 * The original quoted value and the reason are kept for the record:
 *   waived_amount  - quoted value that was waived
 *   waiver_reason  - why the fee was waived (required, shown permanently)
 *   waived_by / waived_at - who waived it and when
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['lease_applications', 'application_payments'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'payment_status')) {
                DB::statement("ALTER TABLE `{$table}` MODIFY `payment_status` ENUM('pending','partial','paid','free') NOT NULL DEFAULT 'pending'");
            }
        }

        Schema::table('lease_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('lease_applications', 'waived_amount')) {
                $table->decimal('waived_amount', 12, 2)->nullable()->after('payment_status');
            }
            if (!Schema::hasColumn('lease_applications', 'waiver_reason')) {
                $table->text('waiver_reason')->nullable()->after('waived_amount');
            }
            if (!Schema::hasColumn('lease_applications', 'waived_by')) {
                $table->unsignedBigInteger('waived_by')->nullable()->after('waiver_reason');
            }
            if (!Schema::hasColumn('lease_applications', 'waived_at')) {
                $table->timestamp('waived_at')->nullable()->after('waived_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lease_applications', function (Blueprint $table) {
            foreach (['waived_at', 'waived_by', 'waiver_reason', 'waived_amount'] as $col) {
                if (Schema::hasColumn('lease_applications', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        foreach (['lease_applications', 'application_payments'] as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'payment_status')) {
                // Free rows fall back to "paid" (nothing due) before the value is removed
                DB::table($table)->where('payment_status', 'free')->update(['payment_status' => 'paid']);
                DB::statement("ALTER TABLE `{$table}` MODIFY `payment_status` ENUM('pending','partial','paid') NOT NULL DEFAULT 'pending'");
            }
        }
    }
};

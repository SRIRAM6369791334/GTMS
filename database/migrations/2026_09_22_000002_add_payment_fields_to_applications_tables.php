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
        // 1. Add payment columns to lease_applications
        if (Schema::hasTable('lease_applications')) {
            Schema::table('lease_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('lease_applications', 'product_value')) {
                    $table->decimal('product_value', 12, 2)->nullable()->default(0.00)->after('area_extent_acres');
                }
                if (!Schema::hasColumn('lease_applications', 'paid_amount')) {
                    $table->decimal('paid_amount', 12, 2)->nullable()->default(0.00)->after('product_value');
                }
                if (!Schema::hasColumn('lease_applications', 'pending_amount')) {
                    $table->decimal('pending_amount', 12, 2)->nullable()->default(0.00)->after('paid_amount');
                }
                if (!Schema::hasColumn('lease_applications', 'payment_status')) {
                    $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending')->after('pending_amount');
                }
            });
        }

        // 2. Add payment columns to mining_applications
        if (Schema::hasTable('mining_applications')) {
            Schema::table('mining_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('mining_applications', 'product_value')) {
                    $table->decimal('product_value', 12, 2)->nullable()->default(0.00)->after('area_extent_ha');
                }
                if (!Schema::hasColumn('mining_applications', 'paid_amount')) {
                    $table->decimal('paid_amount', 12, 2)->nullable()->default(0.00)->after('product_value');
                }
                if (!Schema::hasColumn('mining_applications', 'pending_amount')) {
                    $table->decimal('pending_amount', 12, 2)->nullable()->default(0.00)->after('paid_amount');
                }
                if (!Schema::hasColumn('mining_applications', 'payment_status')) {
                    $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending')->after('pending_amount');
                }
            });
        }

        // 3. Create universal polymorphic application_payments table
        if (!Schema::hasTable('application_payments')) {
            Schema::create('application_payments', function (Blueprint $table) {
                $table->id();
                $table->string('application_type', 50)->nullable()->index(); // e.g. 'lease', 'mining', etc.
                $table->unsignedBigInteger('application_id')->nullable()->index();
                $table->nullableMorphs('payable'); // polymorphic support: payable_type, payable_id
                $table->decimal('product_value', 12, 2)->default(0.00);
                $table->decimal('paid_amount', 12, 2)->default(0.00);
                $table->decimal('pending_amount', 12, 2)->default(0.00);
                $table->enum('payment_status', ['pending', 'partial', 'paid'])->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lease_applications')) {
            Schema::table('lease_applications', function (Blueprint $table) {
                $table->dropColumn(['product_value', 'paid_amount', 'pending_amount', 'payment_status']);
            });
        }

        if (Schema::hasTable('mining_applications')) {
            Schema::table('mining_applications', function (Blueprint $table) {
                $table->dropColumn(['product_value', 'paid_amount', 'pending_amount', 'payment_status']);
            });
        }

        Schema::dropIfExists('application_payments');
    }
};

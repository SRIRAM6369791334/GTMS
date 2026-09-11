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
        // 1. Mineral Stockpiles Table (Quarry Site Inventory)
        Schema::create('mineral_stockpiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quarry_customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('lease_application_id')->constrained('lease_applications')->restrictOnDelete();
            $table->foreignId('mineral_id')->constrained('minerals')->restrictOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->decimal('annual_permitted_quota', 14, 2)->comment('Permitted annual mining quota in CBM/Tonnes');
            $table->decimal('current_stock_cbm', 14, 2)->default(0.00)->comment('Available stock at pithead');
            $table->decimal('total_dispatched_cbm', 14, 2)->default(0.00)->comment('Dispatched cumulative volume');
            $table->string('unit', 20)->default('CBM');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();

            $table->unique(['lease_application_id', 'mineral_id'], 'uq_lease_mineral_stock');
            $table->index(['quarry_customer_id', 'status']);
        });

        // 2. Mineral Stock Entries Table (Stock In: Extractions / Drone Audits)
        Schema::create('mineral_stock_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mineral_stockpile_id')->constrained('mineral_stockpiles')->cascadeOnDelete();
            $table->date('entry_date');
            $table->decimal('quantity', 12, 2);
            $table->enum('source_type', ['quarry_extraction', 'drone_volume_audit', 'manual_adjustment'])->default('quarry_extraction');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['mineral_stockpile_id', 'entry_date']);
        });

        // 3. Mineral Dispatches Table (Stock Out / Seigniorage Tracking)
        Schema::create('mineral_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mineral_stockpile_id')->constrained('mineral_stockpiles')->cascadeOnDelete();
            $table->timestamp('dispatch_date');
            $table->decimal('quantity', 12, 2);
            $table->string('vehicle_number', 50);
            $table->string('driver_name', 100)->nullable();
            $table->string('destination')->nullable();
            $table->decimal('seigniorage_fee_inr', 12, 2)->default(0.00);
            $table->string('challan_no', 100)->nullable();
            $table->enum('status', ['pending', 'approved', 'dispatched', 'cancelled'])->default('dispatched');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['mineral_stockpile_id', 'dispatch_date']);
            $table->index('vehicle_number');
        });

        // 4. Project Flows Table (Validation Stages 6.1 to 6.6 History)
        Schema::create('project_flows', function (Blueprint $table) {
            $table->id();
            $table->string('flowable_type', 100);
            $table->unsignedBigInteger('flowable_id');
            $table->string('step_code', 20)->comment('e.g. 6.1, 6.2, 6.3');
            $table->string('step_name', 100);
            $table->enum('status', ['pending', 'in_progress', 'passed', 'rejected'])->default('pending');
            $table->text('note')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();

            $table->unique(['flowable_type', 'flowable_id', 'step_code'], 'uq_flow_step');
            $table->index(['flowable_type', 'flowable_id', 'status']);
        });

        // 5. Activity Logs Table (Immutable Audit Trail)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('loggable_type', 100)->nullable();
            $table->unsignedBigInteger('loggable_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['loggable_type', 'loggable_id']);
            $table->index('user_id');
            $table->index('created_at');
        });

        // 6. Archived Activity Logs Table
        Schema::create('archived_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->string('loggable_type', 100)->nullable();
            $table->unsignedBigInteger('loggable_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('action', 100);
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('archived_at')->useCurrent();

            $table->index(['loggable_type', 'loggable_id']);
            $table->index('created_at');
        });

        // 7. Notifications Table
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->string('notifiable_type');
            $table->unsignedBigInteger('notifiable_id');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('archived_activity_logs');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('project_flows');
        Schema::dropIfExists('mineral_dispatches');
        Schema::dropIfExists('mineral_stock_entries');
        Schema::dropIfExists('mineral_stockpiles');
    }
};

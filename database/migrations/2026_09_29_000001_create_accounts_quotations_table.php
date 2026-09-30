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
        // 1. Create quotations table
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_number', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('lease_application_id')->nullable()->constrained('lease_applications')->nullOnDelete();

            // Client Snapshot Fields (frozen at generation)
            $table->string('customer_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('gst_number', 50)->nullable();
            $table->text('address')->nullable();

            // Quarry Concession Snapshot Fields
            $table->string('quarry_name')->nullable();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->string('taluk')->nullable();
            $table->string('village')->nullable();
            $table->string('survey_numbers')->nullable();
            $table->decimal('area_extent_ha', 10, 4)->nullable();
            $table->string('mineral_name')->nullable();

            // Financial Fields
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(18.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);

            // Terms and Statutory Scope
            $table->integer('validity_days')->default(30);
            $table->text('payment_terms')->nullable();
            $table->text('exclusions')->nullable();
            $table->text('notes')->nullable();

            // Workflow Status
            $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'converted'])->default('draft');

            // Audit and Multi-tenancy
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'status']);
            $table->index('quotation_number');
        });

        // 2. Create quotation_items table
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->string('service_name');
            $table->string('sac_code', 50)->nullable();
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2)->default(1.00);
            $table->string('unit', 50)->default('Nos');
            $table->decimal('unit_rate', 12, 2)->default(0.00);
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->timestamps();

            $table->index('quotation_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};

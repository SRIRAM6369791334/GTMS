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
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('quotation_id')->nullable()->constrained('quotations')->nullOnDelete();

            // Statutory Application Reference
            $table->string('application_type', 50)->nullable()->comment('lease, mining, environment, ppt, dgps, drone, ec_certificate, ec_compliance, general');
            $table->unsignedBigInteger('application_id')->nullable();

            // Financial Metrics
            $table->decimal('amount_paid', 12, 2)->default(0.00);
            $table->decimal('balance_due', 12, 2)->default(0.00);
            $table->decimal('previous_paid', 12, 2)->default(0.00);

            // Payment Details
            $table->enum('payment_mode', ['Cash', 'Cheque', 'NEFT/RTGS', 'UPI/GPay', 'Other'])->default('Cash');
            $table->string('bank_name')->nullable();
            $table->string('reference_number')->nullable()->comment('Cheque No, UTR / Transaction Ref');
            $table->date('transaction_date')->index();
            $table->text('notes')->nullable();

            // Audit and Multi-tenancy
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id', 'transaction_date']);
            $table->index(['application_type', 'application_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_receipts');
    }
};

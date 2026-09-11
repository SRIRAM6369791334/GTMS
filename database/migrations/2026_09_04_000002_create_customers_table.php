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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->comment('Representative or individual name');
            $table->string('company_name')->nullable()->comment('Quarry or firm name');
            $table->string('mobile_num', 15);
            $table->string('email')->nullable();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('mineral_id')->nullable()->constrained('minerals')->nullOnDelete();
            $table->string('pan', 10);
            $table->string('gstin', 15)->nullable();
            $table->decimal('area', 10, 2)->nullable()->comment('Quarry area in Hectares');
            $table->text('address')->nullable();
            $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Future customer portal user link');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // High-concurrency performance indexes
            $table->index('customer_name');
            $table->index('company_name');
            $table->index('mobile_num');
            $table->index('pan');
            $table->index(['district_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

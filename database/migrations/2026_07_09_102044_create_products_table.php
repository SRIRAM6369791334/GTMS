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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('bar_code')->unique();
            $table->string('pro_name');
            $table->decimal('gst', 5, 2)->default(0);
            $table->decimal('cast_per', 10, 2);
            $table->decimal('mrp', 10, 2);
            $table->string('unit');
            $table->integer('qty')->default(0);
            $table->decimal('discount_1', 5, 2)->default(0);
            $table->decimal('discount_2', 5, 2)->default(0);
            $table->decimal('discount_3', 5, 2)->default(0);
            $table->unsignedBigInteger('cat_id');
            $table->tinyInteger('delete_status')->default(0);
            $table->timestamps();

            $table->foreign('cat_id')
                  ->references('id')
                  ->on('categories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

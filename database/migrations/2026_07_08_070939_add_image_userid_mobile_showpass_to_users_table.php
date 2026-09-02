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
        Schema::table('users', function (Blueprint $table) {
            $table->string('image')->nullable()->after('email');
            $table->string('mobile_num', 15)->nullable()->after('image');
            $table->string('show_password', 255)->nullable()->after('password');
            $table->unsignedBigInteger('user_id')->nullable()->after('id');

            // If user_id references the users table
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['image', 'mobile_num', 'user_id', 'show_password']);
        });
    }
};

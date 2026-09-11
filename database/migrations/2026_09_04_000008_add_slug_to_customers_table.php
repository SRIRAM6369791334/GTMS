<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('company_name');
        });

        // Generate slugs for existing customers
        $customers = DB::table('customers')->get();
        foreach ($customers as $c) {
            $name = !empty($c->company_name) ? $c->company_name : $c->customer_name;
            $baseSlug = Str::slug($name);
            $slug = $baseSlug ?: 'customer-' . $c->id;
            
            // Ensure unique slug
            $existing = DB::table('customers')->where('slug', $slug)->where('id', '!=', $c->id)->exists();
            if ($existing) {
                $slug = $slug . '-' . $c->id;
            }

            DB::table('customers')->where('id', $c->id)->update(['slug' => $slug]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};

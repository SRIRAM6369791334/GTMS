<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Security: users.show_password held every user's password in plaintext.
 * The application no longer writes or reads it (see UserController / User::$hidden).
 * This wipes the stored values. The column itself is kept for now so older
 * images keep booting during a rollback; drop it in a later release.
 *
 * Irreversible by design: plaintext passwords cannot and must not be restored.
 * Every user should reset their password after this runs.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'show_password')) {
            DB::table('users')->whereNotNull('show_password')->update(['show_password' => null]);
        }
    }

    public function down(): void
    {
        // Intentionally empty: the wiped plaintext values are not recoverable.
    }
};

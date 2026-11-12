<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Security: PARIVESH / e-mail portal passwords were stored in plaintext.
 * The models now use the `encrypted` cast; this migration widens the columns
 * (ciphertext is longer than 255 chars) and encrypts existing values in place.
 *
 * Idempotent: values that already decrypt with the current APP_KEY are skipped.
 * IMPORTANT: encrypted values are tied to APP_KEY. Rotating or losing APP_KEY
 * makes these passwords unreadable.
 */
return new class extends Migration
{
    private array $tables = ['customers', 'environment_projects', 'ppt_applications', 'ec_certificates'];

    private array $columns = ['parivesh_password', 'email_password'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            $columns = $this->existingColumns($table);
            if (empty($columns)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($columns) {
                foreach ($columns as $column) {
                    $blueprint->text($column)->nullable()->change();
                }
            });

            $this->transform($table, $columns, function (string $value) {
                return $this->isEncrypted($value) ? null : Crypt::encryptString($value);
            });
        }
    }

    public function down(): void
    {
        // Decrypt back to plaintext so a rolled-back image (without the cast) still works.
        foreach ($this->tables as $table) {
            $columns = $this->existingColumns($table);
            if (empty($columns)) {
                continue;
            }

            $this->transform($table, $columns, function (string $value) {
                try {
                    return Crypt::decryptString($value);
                } catch (DecryptException) {
                    return null; // already plaintext
                }
            });
        }
    }

    private function existingColumns(string $table): array
    {
        if (!Schema::hasTable($table)) {
            return [];
        }

        return array_values(array_filter($this->columns, fn ($c) => Schema::hasColumn($table, $c)));
    }

    /**
     * Apply $fn to each non-empty value; $fn returns the new value, or null to leave it unchanged.
     */
    private function transform(string $table, array $columns, callable $fn): void
    {
        DB::table($table)
            ->select(array_merge(['id'], $columns))
            ->where(function ($q) use ($columns) {
                foreach ($columns as $column) {
                    $q->orWhereNotNull($column);
                }
            })
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($table, $columns, $fn) {
                foreach ($rows as $row) {
                    $changes = [];
                    foreach ($columns as $column) {
                        $value = $row->{$column};
                        if ($value === null || $value === '') {
                            continue;
                        }
                        $new = $fn((string) $value);
                        if ($new !== null) {
                            $changes[$column] = $new;
                        }
                    }
                    if ($changes) {
                        DB::table($table)->where('id', $row->id)->update($changes);
                    }
                }
            });
    }

    private function isEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);

            return true;
        } catch (DecryptException) {
            return false;
        }
    }
};

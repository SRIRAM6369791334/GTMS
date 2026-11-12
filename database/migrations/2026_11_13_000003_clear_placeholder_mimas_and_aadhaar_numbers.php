<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Data integrity: migrations 2026_09_07_050811 and 2026_09_10_102535 filled every
 * existing customer with fake MIMAS and Aadhaar numbers so a unique index could be added.
 * Those values look real but are not.
 *
 * This clears only the exact value each of those migrations generated for that
 * customer id. A value staff later changed is never touched. Both columns are nullable,
 * and the unique indexes allow multiple NULLs.
 *
 * Cleared customer ids are logged to the migration output.
 */
return new class extends Migration
{
    private const SAMPLE_MIMAS = [
        1 => 'TN-MMS-SLM-001',
        2 => 'TN-MMS-CBE-002',
        3 => 'TN-MMS-MDU-003',
        4 => 'TN-MMS-ERD-004',
        5 => 'TN-MMS-TRI-005',
    ];

    private const SAMPLE_AADHAAR = [
        1 => '9876-5432-1001',
        2 => '9876-5432-1002',
        3 => '9876-5432-1003',
        4 => '9876-5432-1004',
        5 => '9876-5432-1005',
    ];

    public function up(): void
    {
        if (!Schema::hasTable('customers')) {
            return;
        }

        $hasMimas = Schema::hasColumn('customers', 'mimas_no');
        $hasAadhaar = Schema::hasColumn('customers', 'aadhaar_no');
        if (!$hasMimas && !$hasAadhaar) {
            return;
        }

        $select = array_values(array_filter([
            'id',
            $hasMimas ? 'mimas_no' : null,
            $hasAadhaar ? 'aadhaar_no' : null,
        ]));

        $cleared = ['mimas_no' => [], 'aadhaar_no' => []];

        DB::table('customers')->select($select)->orderBy('id')->chunkById(500, function ($rows) use ($hasMimas, $hasAadhaar, &$cleared) {
            foreach ($rows as $row) {
                $changes = [];

                if ($hasMimas && $row->mimas_no !== null && $row->mimas_no === $this->placeholderMimas($row->id)) {
                    $changes['mimas_no'] = null;
                    $cleared['mimas_no'][] = $row->id;
                }

                if ($hasAadhaar && $row->aadhaar_no !== null && $row->aadhaar_no === $this->placeholderAadhaar($row->id)) {
                    $changes['aadhaar_no'] = null;
                    $cleared['aadhaar_no'][] = $row->id;
                }

                if ($changes) {
                    DB::table('customers')->where('id', $row->id)->update($changes);
                }
            }
        });

        foreach ($cleared as $column => $ids) {
            if ($ids) {
                echo "  cleared placeholder {$column} for customer ids: " . implode(',', $ids) . PHP_EOL;
            }
        }
    }

    public function down(): void
    {
        // Intentionally empty: the cleared values were fake and must not be restored.
    }

    private function placeholderMimas(int $id): string
    {
        return self::SAMPLE_MIMAS[$id] ?? ('TN-MMS-CUST-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT));
    }

    private function placeholderAadhaar(int $id): string
    {
        return self::SAMPLE_AADHAAR[$id] ?? ('9876-5432-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT));
    }
};

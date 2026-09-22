<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();
        $moduleId = DB::table('modules')->where('code', 'environment')->value('id') ?? 3;

        // 1. Insert B1 Sub Category 2 folders into `folders` table if they don't already exist
        $sc2Folders = [
            ['name' => 'Documents (ToR Letter)', 'sort_order' => 7],
            ['name' => 'Baseline Study', 'sort_order' => 8],
            ['name' => 'Draft (12 Chapters)', 'sort_order' => 9],
            ['name' => 'TNPCB Draft Submission', 'sort_order' => 10],
            ['name' => 'Final EIA Report', 'sort_order' => 11],
            ['name' => 'Uploading File', 'sort_order' => 12],
        ];

        foreach ($sc2Folders as $folder) {
            $existing = DB::table('folders')
                ->where('module_id', $moduleId)
                ->where('name', $folder['name'])
                ->first();

            if (!$existing) {
                DB::table('folders')->insert([
                    'module_id' => $moduleId,
                    'name' => $folder['name'],
                    'sort_order' => $folder['sort_order'],
                    'status' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Fetch all folder IDs for module 3
        $folders = DB::table('folders')->where('module_id', $moduleId)->pluck('id', 'name');

        // 2. Define Document Checklist Fields for B1 and B2
        $checklist = [
            // B2 & B1-SC1: Documents
            'Documents' => [
                '500m radius letter',
                'Existing Pit Letter',
                'Approved Mining Plan Book',
                'Approved Letter',
                '300m radius VAO Statement',
                'School CER',
                'NOC Letters',
                'Others (PAN, Email, Phone, Aadhaar, EC Observation Sheet)',
            ],
            // B2: Site Photographs
            'Site Photographs' => [
                'DGPS Photograph',
                'Fencing Photograph',
                'Greenbelt Photograph',
            ],
            // B2 & B1-SC1: Report
            'Report' => [
                'Location Details',
                'Covering letter',
                'Front Page',
                'Form-1',
                'Pre-feasibility Report',
                'Request TOR',
                'Executive Summary',
                'Baseline Study Report',
                'Hydrogeological Report',
                'Affidavit',
                'Checklist',
                'Checklist for B1/B2 Category',
                'PPT',
            ],
            // B2 & B1-SC1: GIS
            'GIS & Maps' => [
                'GIS Data',
            ],
            // B2 & B1-SC1: Signed Reports
            'Signed Reports' => [
                'Upload Signed Reports',
            ],
            // B2 & B1-SC1: Parivesh
            'PARIVESH Acknowledgements' => [
                'Note Pad (User Id, Password)',
                'Common Application Form',
                'Form 1 / Form 2',
                'Payment receipt',
                'SPCB Demand Note',
            ],
            // B1-SC2: Documents (ToR Letter)
            'Documents (ToR Letter)' => [
                'ToR Letter',
            ],
            // B1-SC2: Baseline Study
            'Baseline Study' => [
                'Air Quality Monitoring',
                'Noise Monitoring',
                'Water Quality Monitoring',
                'Soil Study',
            ],
            // B1-SC2: Draft (12 Chapters)
            'Draft (12 Chapters)' => [
                'Draft Preparation — 12 Chapters',
                'ToR Compliance',
            ],
            // B1-SC2: TNPCB Draft Submission
            'TNPCB Draft Submission' => [
                'Draft EIA & Executive Summary (Tamil & English)',
                'Public Hearing PPT',
                'Public Opinion Poll',
                'Recording of Queries',
            ],
            // B1-SC2: Final EIA Report
            'Final EIA Report' => [
                'Final EIA Report — 12 Chapters',
                'Integrating Public Queries',
                'Action Plan + EMP',
            ],
            // B1-SC2: Uploading File
            'Uploading File' => [
                'Online Submission on PARIVESH Portal',
            ],
        ];

        foreach ($checklist as $folderName => $fields) {
            $folderId = $folders[$folderName] ?? null;
            if (!$folderId) continue;

            $sort = 1;
            foreach ($fields as $fieldName) {
                $exists = DB::table('document_fields')
                    ->where('folder_id', $folderId)
                    ->where('name', $fieldName)
                    ->exists();

                if (!$exists) {
                    DB::table('document_fields')->insert([
                        'folder_id' => $folderId,
                        'nature_of_work_id' => null,
                        'name' => $fieldName,
                        'required' => 1,
                        'sort_order' => $sort++,
                        'status' => 1,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $moduleId = DB::table('modules')->where('code', 'environment')->value('id') ?? 3;
        $folderIds = DB::table('folders')->where('module_id', $moduleId)->pluck('id');

        DB::table('document_fields')->whereIn('folder_id', $folderIds)->delete();
        DB::table('folders')->where('module_id', $moduleId)->where('sort_order', '>=', 7)->delete();
    }
};

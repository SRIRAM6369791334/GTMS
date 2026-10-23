<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnvironmentClearanceDocumentFieldsSeeder extends Seeder
{
    /**
     * Seed statutory document checklist fields for Module 1 (Environmental Clearance).
     * Derived from approved statutory process flow diagrams for Category B1 and Category B2.
     */
    public function run(): void
    {
        $moduleId = DB::table('modules')->where('code', 'environment')->value('id');
        if (!$moduleId) {
            $moduleId = 1;
        }

        // Folder mapping for Module 1
        $folderDefinitions = [
            // Standard / ToR / B2 Folders
            'Documents' => [
                'sort_order' => 1,
                'fields' => [
                    ['name' => '500m Radius Letter', 'required' => true],
                    ['name' => 'Existing Pit Letter', 'required' => true],
                    ['name' => 'Approved Mining Plan Book', 'required' => true],
                    ['name' => 'Approved Mining Plan Letter', 'required' => true],
                    ['name' => '300m Radius VAO Statement', 'required' => true],
                    ['name' => 'School CER Letter', 'required' => false],
                    ['name' => 'NOC Letters (Revenue / Forest / Panchayat)', 'required' => true],
                    ['name' => 'Statutory Credentials (PAN, Aadhaar, Mobile, Email)', 'required' => true],
                    ['name' => 'Observation & Preparation Checklist Sheet', 'required' => false],
                ],
            ],
            'Site Photographs' => [
                'sort_order' => 2,
                'fields' => [
                    ['name' => 'DGPS Survey Photograph', 'required' => true],
                    ['name' => 'Quarry Boundary Fencing Photograph', 'required' => true],
                    ['name' => 'Greenbelt Plantation Photograph', 'required' => true],
                ],
            ],
            'Report' => [
                'sort_order' => 3,
                'fields' => [
                    ['name' => 'Location Details & Site Description', 'required' => true],
                    ['name' => 'Covering Letter', 'required' => true],
                    ['name' => 'Front Page & Title Sheet', 'required' => true],
                    ['name' => 'Form-1 Application', 'required' => true],
                    ['name' => 'Pre-Feasibility Report (PFR)', 'required' => true],
                    ['name' => 'Request for Terms of Reference (ToR)', 'required' => false],
                    ['name' => 'Executive Summary', 'required' => true],
                    ['name' => 'Baseline Environmental Study Report', 'required' => false],
                    ['name' => 'Hydrogeological Report & Water Balance', 'required' => true],
                    ['name' => 'Statutory Undertaking Affidavit', 'required' => true],
                    ['name' => 'Checklist for Category B1 / B2 Appraisal', 'required' => true],
                    ['name' => 'SEAC Defense PPT Presentation', 'required' => true],
                ],
            ],
            'GIS & Maps' => [
                'sort_order' => 4,
                'fields' => [
                    ['name' => 'GIS Spatial Data (KML & Shapefile)', 'required' => true],
                ],
            ],
            'Signed Reports' => [
                'sort_order' => 5,
                'fields' => [
                    ['name' => 'Upload Signed Reports & Certifications', 'required' => true],
                ],
            ],
            'PARIVESH Acknowledgements' => [
                'sort_order' => 6,
                'fields' => [
                    ['name' => 'PARIVESH Portal Access Credentials (User ID & Password)', 'required' => true],
                    ['name' => 'Common Application Form (CAF)', 'required' => true],
                    ['name' => 'Form-1 / Form-2 Submission Copy', 'required' => true],
                    ['name' => 'PARIVESH Scrutiny Fee Payment Receipt', 'required' => true],
                    ['name' => 'SPCB Demand Note & Challan', 'required' => false],
                ],
            ],

            // ETA Stage Folders (Stage 2 for Category B1)
            'Documents (ToR Letter)' => [
                'sort_order' => 7,
                'fields' => [
                    ['name' => 'ToR Letter', 'required' => true],
                ],
            ],
            'Baseline Study' => [
                'sort_order' => 8,
                'fields' => [
                    ['name' => 'Air Quality Monitoring', 'required' => true],
                    ['name' => 'Noise Monitoring', 'required' => true],
                    ['name' => 'Water Quality Monitoring', 'required' => true],
                    ['name' => 'Soil Study', 'required' => true],
                ],
            ],
            'Draft (12 Chapters)' => [
                'sort_order' => 9,
                'fields' => [
                    ['name' => 'Draft Preparation — 12 Chapters', 'required' => true],
                    ['name' => 'ToR Compliance', 'required' => true],
                ],
            ],
            'TNPCB Draft Submission' => [
                'sort_order' => 10,
                'fields' => [
                    ['name' => 'Draft EIA & Executive Summary (Tamil & English)', 'required' => true],
                    ['name' => 'Public Hearing PPT', 'required' => true],
                    ['name' => 'Public Opinion Poll', 'required' => true],
                    ['name' => 'Recording of Queries', 'required' => true],
                ],
            ],
            'Final EIA Report' => [
                'sort_order' => 11,
                'fields' => [
                    ['name' => 'Final EIA Report — 12 Chapters', 'required' => true],
                    ['name' => 'Integrating Public Queries', 'required' => true],
                    ['name' => 'Action Plan + EMP', 'required' => true],
                ],
            ],
            'Uploading File' => [
                'sort_order' => 12,
                'fields' => [
                    ['name' => 'Online Submission on PARIVESH Portal', 'required' => true],
                ],
            ],
        ];

        foreach ($folderDefinitions as $folderName => $config) {
            // Find or create folder in Module 1
            $folder = DB::table('folders')
                ->where('module_id', $moduleId)
                ->where('name', $folderName)
                ->first();

            if (!$folder) {
                $folderId = DB::table('folders')->insertGetId([
                    'module_id'   => $moduleId,
                    'name'        => $folderName,
                    'sort_order'  => $config['sort_order'],
                    'status'      => 1,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            } else {
                $folderId = $folder->id;
                DB::table('folders')->where('id', $folderId)->update([
                    'sort_order' => $config['sort_order'],
                    'status'     => 1,
                    'updated_at' => now(),
                ]);
            }

            // Seed fields for this folder
            foreach ($config['fields'] as $fIdx => $fieldDef) {
                DB::table('document_fields')->updateOrInsert(
                    [
                        'folder_id' => $folderId,
                        'name'      => $fieldDef['name'],
                    ],
                    [
                        'required'   => $fieldDef['required'],
                        'sort_order' => $fIdx + 1,
                        'status'     => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}

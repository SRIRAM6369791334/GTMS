<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GtmsMasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed 38 Tamil Nadu Districts
        $districts = [
            ['name' => 'Ariyalur', 'code' => 'ARL'],
            ['name' => 'Chengalpattu', 'code' => 'CGL'],
            ['name' => 'Chennai', 'code' => 'CHN'],
            ['name' => 'Coimbatore', 'code' => 'CBE'],
            ['name' => 'Cuddalore', 'code' => 'CUD'],
            ['name' => 'Dharmapuri', 'code' => 'DPI'],
            ['name' => 'Dindigul', 'code' => 'DGL'],
            ['name' => 'Erode', 'code' => 'ERD'],
            ['name' => 'Kallakurichi', 'code' => 'KLK'],
            ['name' => 'Kancheepuram', 'code' => 'KCP'],
            ['name' => 'Karur', 'code' => 'KRR'],
            ['name' => 'Krishnagiri', 'code' => 'KGI'],
            ['name' => 'Madurai', 'code' => 'MDU'],
            ['name' => 'Mayiladuthurai', 'code' => 'MYD'],
            ['name' => 'Nagapattinam', 'code' => 'NGP'],
            ['name' => 'Kanniyakumari', 'code' => 'KKI'],
            ['name' => 'Namakkal', 'code' => 'NKL'],
            ['name' => 'Perambalur', 'code' => 'PBL'],
            ['name' => 'Pudukkottai', 'code' => 'PDK'],
            ['name' => 'Ramanathapuram', 'code' => 'RMD'],
            ['name' => 'Ranipet', 'code' => 'RPT'],
            ['name' => 'Salem', 'code' => 'SLM'],
            ['name' => 'Sivagangai', 'code' => 'SVG'],
            ['name' => 'Tenkasi', 'code' => 'TKS'],
            ['name' => 'Thanjavur', 'code' => 'TNJ'],
            ['name' => 'Theni', 'code' => 'THI'],
            ['name' => 'Thiruvallur', 'code' => 'TLR'],
            ['name' => 'Thiruvarur', 'code' => 'TVR'],
            ['name' => 'Thoothukudi', 'code' => 'TKD'],
            ['name' => 'Tiruchirappalli', 'code' => 'TRY'],
            ['name' => 'Tirunelveli', 'code' => 'TNV'],
            ['name' => 'Tirupathur', 'code' => 'TPR'],
            ['name' => 'Tiruppur', 'code' => 'TUP'],
            ['name' => 'Tiruvannamalai', 'code' => 'TVM'],
            ['name' => 'Nilgiris', 'code' => 'NLG'],
            ['name' => 'Vellore', 'code' => 'VLR'],
            ['name' => 'Viluppuram', 'code' => 'VPM'],
            ['name' => 'Virudhunagar', 'code' => 'VNR'],
        ];

        foreach ($districts as $district) {
            DB::table('districts')->updateOrInsert(
                ['name' => $district['name']],
                [
                    'code' => $district['code'],
                    'state' => 'Tamil Nadu',
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 2. Seed Minerals
        $minerals = [
            ['name' => 'Rough Stone', 'category' => 'Minor', 'default_unit' => 'CBM'],
            ['name' => 'Gravel', 'category' => 'Minor', 'default_unit' => 'CBM'],
            ['name' => 'Granite', 'category' => 'Minor', 'default_unit' => 'CBM'],
            ['name' => 'Lime Stone', 'category' => 'Major', 'default_unit' => 'Tonnes'],
            ['name' => 'Fire Clay', 'category' => 'Minor', 'default_unit' => 'Tonnes'],
            ['name' => 'Quartz', 'category' => 'Minor', 'default_unit' => 'Tonnes'],
            ['name' => 'Feldspar', 'category' => 'Minor', 'default_unit' => 'Tonnes'],
            ['name' => 'Others', 'category' => 'Minor', 'default_unit' => 'CBM'],
        ];

        foreach ($minerals as $mineral) {
            DB::table('minerals')->updateOrInsert(
                ['name' => $mineral['name']],
                [
                    'category' => $mineral['category'],
                    'default_unit' => $mineral['default_unit'],
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 3. Seed Lease Categories (TN Minor Mineral Rules)
        $categories = [
            ['code' => 'Rule 12 (2-A)(a)', 'name' => 'TNMIR Rule 12 (2-A)(a) — Renewal of Quarry Lease', 'land_type' => 'Poramboke'],
            ['code' => 'Rule 19(1)',        'name' => 'TNMIR Rule 19(1) — Grant of Quarry Lease in Patta Land', 'land_type' => 'Patta'],
            ['code' => 'Rule 19(2)(a)',     'name' => 'TNMIR Rule 19(2)(a) — Quarry Lease (Government Land)',  'land_type' => 'Poramboke'],
            ['code' => 'Rule 19-A',         'name' => 'TNMIR Rule 19-A — Special Concessions for Granite / Mineral', 'land_type' => 'Both'],
            ['code' => 'Rule 36-F',         'name' => 'TNMIR Rule 36-F — Transport Permit Related Lease', 'land_type' => 'Both'],
            ['code' => 'Rule 44',           'name' => 'TNMIR Rule 44 — Quarrying of Minor Minerals',        'land_type' => 'Both'],
            ['code' => 'Rule 7',            'name' => 'TNMIR Rule 7 — Small Quarry Permit / General Conditions', 'land_type' => 'Patta'],
            ['code' => 'MDCC',              'name' => 'Mining Dues Clearance Certificate',                  'land_type' => 'Both'],
        ];

        foreach ($categories as $cat) {
            DB::table('lease_categories')->updateOrInsert(
                ['code' => $cat['code']],
                [
                    'name' => $cat['name'],
                    'land_type' => $cat['land_type'],
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 4. Seed Plan Types
        $planTypes = [
            'Mining Plan',
            'Revised Mining Plan',
            'Modified Mining Plan',
            'Scheme of Mining',
        ];

        foreach ($planTypes as $pt) {
            DB::table('plan_types')->updateOrInsert(
                ['name' => $pt],
                ['status' => 1, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // 5. Seed Applicant Types
        $applicantTypes = [
            'Individual',
            'Partnership Firm',
            'Private Limited Company',
            'Public Limited Company',
            'Trust / Society',
        ];

        foreach ($applicantTypes as $at) {
            DB::table('applicant_types')->updateOrInsert(
                ['name' => $at],
                ['status' => 1, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // 6. Seed Modules
        $modules = [
            ['code' => 'lease', 'name' => 'Lease Application'],
            ['code' => 'mining', 'name' => 'Mining Application'],
            ['code' => 'environment', 'name' => 'Environmental Clearance'],
            ['code' => 'ec', 'name' => 'EC Certificate'],
            ['code' => 'ppt', 'name' => 'PPT Department'],
            ['code' => 'dgps', 'name' => 'DGPS Survey'],
            ['code' => 'drone', 'name' => 'Drone Survey'],
        ];

        foreach ($modules as $mod) {
            DB::table('modules')->updateOrInsert(
                ['code' => $mod['code']],
                ['name' => $mod['name'], 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // 7. Seed Standard Folders per Module
        $foldersByModule = [
            'mining' => ['Field Log', 'Documents', 'Site Photos', 'Report', 'Plan', 'Others'],
            'lease' => ['Documents', 'Lease Application', 'Plan'],
            'environment' => ['Documents', 'Site Photographs', 'Report', 'GIS & Maps', 'Signed Reports', 'PARIVESH Acknowledgements'],
            'ppt' => [
                'Documents',
                'EDS & EDS Reply',
                'Demand Note & Challan',
                'File Number Details',
                'SEAC Agenda',
                'SEAC Minutes',
                'ADS & ADS Reply',
                'CER Affidavit',
                'SEIAA Agenda',
                'SEIAA Minutes',
                'Environmental Clearance',
            ],
            'dgps' => ['Field Data', 'Raw GPS Data', 'GTM Report', 'AutoCAD Maps'],
            'drone' => ['Flight Logs', 'Orthomosaic Maps', 'Contour DXF', '3D Mesh', 'Volume Report'],
        ];

        foreach ($foldersByModule as $modCode => $folderList) {
            $moduleId = DB::table('modules')->where('code', $modCode)->value('id');
            if ($moduleId) {
                foreach ($folderList as $index => $folderName) {
                    $folderId = DB::table('folders')->updateOrInsert(
                        ['module_id' => $moduleId, 'name' => $folderName],
                        ['sort_order' => $index + 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }

        // 8. Seed Complete Document Fields for Lease Folders according to Blueprint
        $leaseModuleId = DB::table('modules')->where('code', 'lease')->value('id');
        if ($leaseModuleId) {
            // Folder 1: Documents (18 Blueprint Items)
            $docFolderId = DB::table('folders')->where('name', 'Documents')->where('module_id', $leaseModuleId)->value('id');
            if ($docFolderId) {
                $docFields = [
                    'Land Document',
                    'Patta',
                    'Adangal',
                    'A-Register',
                    'Encumbrance Certificate',
                    'FMB (Field Measurement Book)',
                    'IT returns',
                    'Consent Land Document',
                    'Aadhaar Card',
                    'PAN Card',
                    'Partnership Deed',
                    'Company PAN Card',
                    'GST copy',
                    'Work Order',
                    'Gazette',
                    'Recommendation letter',
                    'Terms & Condition of contract',
                    'History of Previous Quarry',
                ];
                foreach ($docFields as $idx => $fieldName) {
                    DB::table('document_fields')->updateOrInsert(
                        ['folder_id' => $docFolderId, 'name' => $fieldName],
                        ['required' => true, 'sort_order' => $idx + 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }

            // Folder 2: Lease Application (6 Blueprint Items)
            $leaseAppFolderId = DB::table('folders')->where('name', 'Lease Application')->where('module_id', $leaseModuleId)->value('id');
            if ($leaseAppFolderId) {
                $appFields = [
                    'Lease application Form',
                    'Affidavit - Mining Dues',
                    'Affidavit - Income tax',
                    'Affidavit - Mining Lease',
                    'Affidavit - 1.5-meter depth',
                    'Affidavit - Hill areas',
                ];
                foreach ($appFields as $idx => $fieldName) {
                    DB::table('document_fields')->updateOrInsert(
                        ['folder_id' => $leaseAppFolderId, 'name' => $fieldName],
                        ['required' => true, 'sort_order' => $idx + 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }

            // Folder 3: Plan (3 Blueprint Items)
            $planFolderId = DB::table('folders')->where('name', 'Plan')->where('module_id', $leaseModuleId)->value('id');
            if ($planFolderId) {
                $planFields = [
                    'Plan Source File',
                    'KML File',
                    'Plan PDF',
                ];
                foreach ($planFields as $idx => $fieldName) {
                    DB::table('document_fields')->updateOrInsert(
                        ['folder_id' => $planFolderId, 'name' => $fieldName],
                        ['required' => true, 'sort_order' => $idx + 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }
    }
}

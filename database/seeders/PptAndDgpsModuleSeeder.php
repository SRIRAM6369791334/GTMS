<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;
use App\Models\PptApplication;
use App\Models\PptDocument;
use App\Models\DgpsSurvey;
use App\Models\SurveyDocument;
use App\Models\ApplicationHandler;
use App\Models\ApplicationPayment;
use App\Models\Folder;

class PptAndDgpsModuleSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        if ($customers->isEmpty()) {
            return;
        }

        $districts = District::all();
        $minerals = Mineral::all();
        $folder = Folder::first();
        $folderId = $folder ? $folder->id : 1;

        // ==========================================
        // 1. SEED PPT APPLICATIONS
        // ==========================================
        $pptSamples = [
            [
                'no'       => 'PPT-2026-0042',
                'cust_idx' => 0,
                'project'  => 'Semmandapatti Rough Stone & Gravel Presentation',
                'village'  => 'Semmandapatti Village, Omalur Taluk',
                'status'   => 'approved',
                'val'      => 45000.00,
                'paid'     => 45000.00,
                'p_status' => 'paid',
            ],
            [
                'no'       => 'PPT-2026-0041',
                'cust_idx' => min(1, $customers->count() - 1),
                'project'  => 'Perundurai Multi-Mineral SEAC Presentation',
                'village'  => 'Perundurai Taluk',
                'status'   => 'presented',
                'val'      => 40000.00,
                'paid'     => 25000.00,
                'p_status' => 'partial',
            ],
            [
                'no'       => 'PPT-2026-0040',
                'cust_idx' => min(2, $customers->count() - 1),
                'project'  => 'Melur Granite Technical Presentation Dossier',
                'village'  => 'Melur Taluk, Madurai',
                'status'   => 'agenda_scheduled',
                'val'      => 50000.00,
                'paid'     => 50000.00,
                'p_status' => 'paid',
            ],
            [
                'no'       => 'PPT-2026-0039',
                'cust_idx' => min(3, $customers->count() - 1),
                'project'  => 'Kinathukadavu Blue Metal SEIAA Online Process',
                'village'  => 'Kinathukadavu Village',
                'status'   => 'draft',
                'val'      => 35000.00,
                'paid'     => 0.00,
                'p_status' => 'pending',
            ],
        ];

        foreach ($pptSamples as $sample) {
            if (PptApplication::where('application_no', $sample['no'])->exists()) continue;
            $cust = $customers[$sample['cust_idx']] ?? $customers->first();
            $dist = $districts->random();
            $min = $minerals->isNotEmpty() ? $minerals->random()->id : 1;

            $ppt = PptApplication::create([
                'application_no'        => $sample['no'],
                'customer_id'           => $cust->id,
                'project_name'          => $sample['project'],
                'district_id'           => $dist->id,
                'taluk_village'         => $sample['village'],
                'mineral_id'            => $min,
                'status'                => $sample['status'],
                'product_value'         => $sample['val'],
                'paid_amount'           => $sample['paid'],
                'pending_amount'        => max(0, $sample['val'] - $sample['paid']),
                'payment_status'        => $sample['p_status'],
                'rqp_attending'         => 'Er. M. Senthil Kumar (RQP #0812)',
                'company_rep_attending' => $cust->contact_name ?: $cust->customer_name,
                'rep_mobile'            => $cust->mobile_num ?: '9876543210',
            ]);

            // Add Handlers
            ApplicationHandler::create([
                'application_type' => 'ppt',
                'application_id'   => $ppt->id,
                'name'             => 'Dr. K. Ravichandran',
                'role'             => 'Lead Environmental Presentation Expert',
                'notes'            => 'SEAC Presentation, EDS query compliance & ToR defense',
                'sort_order'       => 1,
            ]);
            ApplicationHandler::create([
                'application_type' => 'ppt',
                'application_id'   => $ppt->id,
                'name'             => 'S. Manoharan',
                'role'             => 'RQP GIS & KML Specialist',
                'notes'            => '10km Radius map, cluster study superimposition',
                'sort_order'       => 2,
            ]);

            // Add Payment
            ApplicationPayment::create([
                'application_type' => 'ppt',
                'application_id'   => $ppt->id,
                'product_value'    => $sample['val'],
                'paid_amount'      => $sample['paid'],
                'pending_amount'   => max(0, $sample['val'] - $sample['paid']),
                'payment_status'   => $sample['p_status'],
                'notes'            => 'Initial consultation and presentation fee',
            ]);
        }

        // ==========================================
        // 2. SEED DGPS SURVEYS
        // ==========================================
        $dgpsSamples = [
                [
                    'no'       => 'DGPS-2026-0031',
                    'cust_idx' => 0,
                    'loc'      => 'Salem / Semmandapatti',
                    'l_area'   => 4.25,
                    's_area'   => 4.24,
                    'status'   => 'completed',
                    'r_status' => 'verified',
                    'val'      => 35000.00,
                    'paid'     => 35000.00,
                    'p_status' => 'paid',
                ],
                [
                    'no'       => 'DGPS-2026-0030',
                    'cust_idx' => min(1, $customers->count() - 1),
                    'loc'      => 'Erode / Perundurai',
                    'l_area'   => 2.80,
                    's_area'   => 2.81,
                    'status'   => 'in_progress',
                    'r_status' => 'pending',
                    'val'      => 30000.00,
                    'paid'     => 15000.00,
                    'p_status' => 'partial',
                ],
                [
                    'no'       => 'DGPS-2026-0029',
                    'cust_idx' => min(2, $customers->count() - 1),
                    'loc'      => 'Madurai / Melur',
                    'l_area'   => 6.10,
                    's_area'   => 6.09,
                    'status'   => 'completed',
                    'r_status' => 'verified',
                    'val'      => 45000.00,
                    'paid'     => 45000.00,
                    'p_status' => 'paid',
                ],
                [
                    'no'       => 'DGPS-2026-0028',
                    'cust_idx' => min(3, $customers->count() - 1),
                    'loc'      => 'Coimbatore / Kinathukadavu',
                    'l_area'   => 1.75,
                    's_area'   => 1.75,
                    'status'   => 'scheduled',
                    'r_status' => 'pending',
                    'val'      => 25000.00,
                    'paid'     => 0.00,
                    'p_status' => 'pending',
                ],
            ];

        foreach ($dgpsSamples as $sample) {
            if (DgpsSurvey::where('survey_no', $sample['no'])->exists()) continue;
            $cust = $customers[$sample['cust_idx']] ?? $customers->first();

            $dgps = DgpsSurvey::create([
                'survey_no'            => $sample['no'],
                'field_book_no'        => 'FB-TN-' . rand(100, 999),
                'customer_id'          => $cust->id,
                'lease_area_ha'        => $sample['l_area'],
                'surveyed_area_ha'     => $sample['s_area'],
                'area_discrepancy_ha'  => abs($sample['l_area'] - $sample['s_area']),
                'location'             => $sample['loc'],
                'survey_date'          => date('Y-m-d', strtotime('-' . rand(2, 20) . ' days')),
                'instrument_model'     => 'Trimble R12i GNSS RTK Base & Rover',
                'instrument_serial_no' => 'SN-TRM-889102',
                'survey_status'        => $sample['status'],
                'report_status'        => $sample['r_status'],
                'product_value'        => $sample['val'],
                'paid_amount'          => $sample['paid'],
                'pending_amount'       => max(0, $sample['val'] - $sample['paid']),
                'payment_status'       => $sample['p_status'],
                'survey_team_notes'    => 'SOI Benchmark fixed at Pillar #1, rover calibration verified within 5mm tolerance',
            ]);

            // Handlers
            ApplicationHandler::create([
                'application_type' => 'dgps',
                'application_id'   => $dgps->id,
                'name'             => 'Er. A. Vijayakumar',
                'role'             => 'Chief Land Surveyor',
                'notes'            => 'Base station setup, static observation & benchmark fixation',
                'sort_order'       => 1,
            ]);
            ApplicationHandler::create([
                'application_type' => 'dgps',
                'application_id'   => $dgps->id,
                'name'             => 'K. Prakash',
                'role'             => 'GIS / CAD Mapping Engineer',
                'notes'            => 'Rover points post-processing & Cadastral FMB overlay',
                'sort_order'       => 2,
            ]);

            // Payment
            ApplicationPayment::create([
                'application_type' => 'dgps',
                'application_id'   => $dgps->id,
                'product_value'    => $sample['val'],
                'paid_amount'      => $sample['paid'],
                'pending_amount'   => max(0, $sample['val'] - $sample['paid']),
                'payment_status'   => $sample['p_status'],
                'notes'            => 'Field survey charges settled',
            ]);
        }
    }
}

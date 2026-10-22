<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\District;
use App\Models\EnvironmentProject;
use App\Models\PptApplication;
use App\Models\ApplicationHandler;
use App\Http\Controllers\EnverionsoneController;

class VisualEcDemoSeeder extends Seeder
{
    public function run(): void
    {
        $district = District::firstOrCreate(
            ['name' => 'Salem'],
            ['state_name' => 'Tamil Nadu', 'status' => 1]
        );

        $customer = Customer::firstOrCreate(
            ['mobile_num' => '9840123456'],
            [
                'customer_name' => 'K. Murugan',
                'company_name'  => 'Murugan Blue Metal & Rough Stone Works',
                'mimas_no'      => 'TN-MMS-SLM-0088',
                'district_id'   => $district->id,
                'address'       => 'Pagalpatti, Omalur Taluk, Salem District',
                'status'        => 1,
            ]
        );

        // 1. Category B1 Demo Project: In Stage 1 TOR Preparation
        $b1TorProject = EnvironmentProject::create([
            'project_code'  => 'ENV-B1-2026-0001',
            'customer_id'   => $customer->id,
            'category'      => 'B1',
            'sub_category'  => 'TOR',
            'b1_stage'      => 'sc1_prep',
            'project_name'  => 'Murugan Rough Stone & Gravel Quarry (8.50.0 Ha)',
            'district_id'   => $district->id,
            'location'      => 'S.F. Nos. 142/1, 142/2, Pagalpatti Village, Omalur Taluk',
            'contact_name'  => 'K. Murugan',
            'contact_phone' => '9840123456',
            'status'        => 'draft',
            'product_value' => 75000.00,
            'paid_amount'   => 75000.00,
            'pending_amount'=> 0.00,
            'payment_status'=> 'paid',
            'branch_id'     => 1,
        ]);
        EnverionsoneController::generateSlots($b1TorProject);

        // 2. Category B2 Demo Project: In Draft ready to Submit to PPT Department
        $b2Project = EnvironmentProject::create([
            'project_code'  => 'ENV-B2-2026-0002',
            'customer_id'   => $customer->id,
            'category'      => 'B2',
            'sub_category'  => null,
            'b1_stage'      => 'sc1_prep',
            'project_name'  => 'Selvam Granites Concession Quarry (2.10.0 Ha)',
            'district_id'   => $district->id,
            'location'      => 'S.F. Nos. 78/3B, Semmandapatti Village, Omalur Taluk',
            'contact_name'  => 'R. Selvam',
            'contact_phone' => '9443123456',
            'status'        => 'draft',
            'product_value' => 50000.00,
            'paid_amount'   => 50000.00,
            'pending_amount'=> 0.00,
            'payment_status'=> 'paid',
            'branch_id'     => 1,
        ]);
        EnverionsoneController::generateSlots($b2Project);

        // 3. Category B2 Project in PPT Appraisal
        $pptApp = PptApplication::create([
            'application_no'         => 'PPT-2026-0001',
            'customer_id'            => $customer->id,
            'environment_project_id' => null, // will link
            'presentation_stage'     => 'b2_presentation',
            'project_name'           => 'Sri Krishna Blue Metal Quarry (4.85.0 Ha) (Category B2: SEAC EC Presentation)',
            'district_id'            => $district->id,
            'taluk_village'          => 'Kollapatty Village, Salem Taluk',
            'mineral_id'             => 1,
            'status'                 => 'agenda_scheduled',
            'product_value'          => 25000.00,
            'paid_amount'            => 25000.00,
            'pending_amount'         => 0.00,
            'payment_status'         => 'paid',
            'branch_id'              => 1,
        ]);

        $b2InPptProject = EnvironmentProject::create([
            'project_code'   => 'ENV-B2-2026-0003',
            'customer_id'    => $customer->id,
            'category'       => 'B2',
            'sub_category'   => null,
            'b1_stage'       => 'sc1_prep',
            'ppt_stage_1_id' => $pptApp->id,
            'project_name'   => 'Sri Krishna Blue Metal Quarry (4.85.0 Ha)',
            'district_id'    => $district->id,
            'location'       => 'S.F. Nos. 215/1A, Kollapatty Village, Salem Taluk',
            'contact_name'   => 'M. Krishnan',
            'contact_phone'  => '9842123456',
            'status'         => 'validation',
            'product_value'  => 50000.00,
            'paid_amount'    => 50000.00,
            'pending_amount' => 0.00,
            'payment_status' => 'paid',
            'branch_id'      => 1,
        ]);
        $pptApp->update(['environment_project_id' => $b2InPptProject->id]);
        EnverionsoneController::generateSlots($b2InPptProject);

        ApplicationHandler::create([
            'application_type' => 'ppt',
            'application_id'   => $pptApp->id,
            'name'             => 'Dr. K. Ravichandran',
            'role'             => 'Lead Technical Consultant',
            'notes'            => 'SEAC Category B2 EC presentation defense',
            'sort_order'       => 1,
        ]);
    }
}

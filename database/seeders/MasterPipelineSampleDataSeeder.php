<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\DgpsSurvey;
use App\Models\District;
use App\Models\DroneSurvey;
use App\Models\EnvironmentProject;
use App\Models\LeaseApplication;
use App\Models\LeaseCategory;
use App\Models\MasterProject;
use App\Models\Mineral;
use App\Models\MiningApplication;
use App\Models\PipelineMovement;
use App\Models\PptApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MasterPipelineSampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branch = Branch::first() ?? Branch::create([
            'branch_name' => 'Salem Regional Directorate',
            'branch_code' => 'SLM-01',
            'status'      => 1,
        ]);

        $adminUser = User::where('email', 'admin@gtms.com')->first()
            ?? User::where('role_id', 1)->first()
            ?? User::first();

        $officerUser = User::where('email', 'officer@gtms.com')->first() ?? $adminUser;

        $salemDistrict = District::where('name', 'Salem')->first() ?? District::first();
        $dharmapuriDistrict = District::where('name', 'Dharmapuri')->first() ?? $salemDistrict;
        $tirunelveliDistrict = District::where('name', 'Tirunelveli')->first() ?? $salemDistrict;
        $tvmDistrict = District::where('name', 'Tiruvannamalai')->first() ?? $salemDistrict;
        $maduraiDistrict = District::where('name', 'Madurai')->first() ?? $salemDistrict;

        $graniteMineral = Mineral::where('name', 'like', '%Granite%')->first()
            ?? Mineral::firstOrCreate(['name' => 'Black Granite'], ['category' => 'Minor', 'default_unit' => 'CBM', 'status' => 1]);

        $roughStoneMineral = Mineral::where('name', 'like', '%Stone%')->first()
            ?? Mineral::firstOrCreate(['name' => 'Rough Stone & Gravel'], ['category' => 'Minor', 'default_unit' => 'CBM', 'status' => 1]);

        $leaseCat = LeaseCategory::first() ?? LeaseCategory::create([
            'code'      => 'Rule 19(1)',
            'name'      => 'TNMIR Rule 19(1) — Grant of Quarry Lease in Patta Land',
            'land_type' => 'Patta',
            'status'    => 1,
        ]);

        // Clean existing demo sample master projects if re-seeding
        $demoIds = ['GTMS-2026-8085', 'GTMS-2026-8091', 'GTMS-2026-8104', 'GTMS-2026-8050', 'GTMS-2026-8112', 'GTMS-2026-0085', 'GTMS-2026-0091', 'GTMS-2026-0104', 'GTMS-2026-0050', 'GTMS-2026-0112'];
        $existing = MasterProject::withoutGlobalScopes()->whereIn('master_application_id', $demoIds)->get();
        foreach ($existing as $p) {
            PipelineMovement::where('master_project_id', $p->id)->delete();
            $p->forceDelete();
        }

        // =========================================================================
        // PROJECT 1: GTMS-2026-8085 (RETURNED FOR CORRECTION - SEAC TO MINING)
        // =========================================================================
        $cust1 = Customer::firstOrCreate(
            ['company_name' => 'Kaveri Blue Metals Private Limited'],
            [
                'customer_name' => 'Thiru. K. Sengottaiyan',
                'mobile_num'    => '9842711001',
                'email'         => 'kaveribluemetals@gmail.com',
                'gstin'         => '33AAACK4512B1Z3',
                'branch_id'     => $branch->id,
                'created_by'    => $adminUser->id,
            ]
        );

        $lease1 = LeaseApplication::create([
            'application_no'    => 'LA-2026-8042',
            'customer_id'       => $cust1->id,
            'district_id'       => $salemDistrict->id,
            'mineral_id'        => $roughStoneMineral->id,
            'category_id'       => $leaseCat->id,
            'taluk'             => 'Omalur',
            'village'           => 'Pagalpatti',
            'area_extent_ha'    => '4.85.0',
            'branch_id'         => $branch->id,
            'status'            => 'approved',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(15),
        ]);

        $dgps1 = DgpsSurvey::create([
            'survey_no'         => 'DS-2026-8018',
            'field_book_no'     => 'FB-SLM-2026-8018',
            'customer_id'       => $cust1->id,
            'location'          => 'Pagalpatti Village, Omalur Taluk',
            'surveyed_area_ha'  => 4.85,
            'branch_id'         => $branch->id,
            'survey_status'     => 'completed',
            'report_status'     => 'verified',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(12),
        ]);

        $mining1 = MiningApplication::create([
            'application_no'    => 'MP-2026-8029',
            'lease_application_id' => $lease1->id,
            'customer_id'       => $cust1->id,
            'district_id'       => $salemDistrict->id,
            'mineral_id'        => $roughStoneMineral->id,
            'branch_id'         => $branch->id,
            'status'            => 'returned_for_correction',
            'status_notes'      => 'SEAC Technical Appraisal committee noted excavation bench slope exceeds 45 degrees without berm terrace. Revise bench height to 6m with 6m berm terraces in Plate No. 4.',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(8),
        ]);

        $env1 = EnvironmentProject::create([
            'project_code'      => 'ENV-2026-8015',
            'project_name'      => 'Pagalpatti Rough Stone Quarry EC Clearance',
            'customer_id'       => $cust1->id,
            'district_id'       => $salemDistrict->id,
            'category'          => 'B2',
            'branch_id'         => $branch->id,
            'status'            => 'approved',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(5),
        ]);

        $ppt1 = PptApplication::create([
            'application_no'    => 'PPT-2026-8008',
            'project_name'      => 'SEAC Presentation Dossier — Kaveri Blue Metals',
            'customer_id'       => $cust1->id,
            'district_id'       => $salemDistrict->id,
            'mineral_id'        => $roughStoneMineral->id,
            'branch_id'         => $branch->id,
            'status'            => 'scheduled',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(3),
        ]);

        $master1 = MasterProject::create([
            'master_application_id'  => 'GTMS-2026-8085',
            'customer_id'            => $cust1->id,
            'branch_id'              => $branch->id,
            'district_id'            => $salemDistrict->id,
            'taluk'                  => 'Omalur',
            'village'                => 'Pagalpatti',
            'survey_numbers'         => '142/1A, 142/1B, 143/2',
            'area_extent_ha'         => 4.85,
            'mineral_name'           => 'Rough Stone & Gravel',
            'lease_application_id'   => $lease1->id,
            'dgps_survey_id'         => $dgps1->id,
            'mining_application_id'  => $mining1->id,
            'environment_project_id' => $env1->id,
            'ppt_application_id'     => $ppt1->id,
            'current_stage'          => 'mining',
            'pipeline_status'        => 'returned_for_correction',
            'created_by'             => $adminUser->id,
            'created_at'             => Carbon::now()->subDays(15),
        ]);

        $lease1->update(['master_project_id' => $master1->id, 'common_id' => 'GTMS-2026-8085']);
        $dgps1->update(['master_project_id' => $master1->id, 'common_id' => 'GTMS-2026-8085']);
        $mining1->update(['master_project_id' => $master1->id, 'common_id' => 'GTMS-2026-8085']);
        $env1->update(['master_project_id' => $master1->id, 'common_id' => 'GTMS-2026-8085']);
        $ppt1->update(['master_project_id' => $master1->id, 'common_id' => 'GTMS-2026-8085']);

        // Movements for Project 1
        PipelineMovement::create([
            'master_project_id' => $master1->id,
            'from_department'   => 'lease',
            'to_department'     => 'survey_dgps',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => 'Lease application scrutiny completed. Revenue records Patta, Chitta, FMB vetted.',
            'officer_id'        => $adminUser->id,
            'officer_name'      => $adminUser->name,
            'created_at'        => Carbon::now()->subDays(12),
        ]);

        PipelineMovement::create([
            'master_project_id' => $master1->id,
            'from_department'   => 'survey_dgps',
            'to_department'     => 'mining',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => 'Cadastral boundary demarcation finalized. P1 to P8 coordinates certified.',
            'officer_id'        => $officerUser->id,
            'officer_name'      => $officerUser->name,
            'created_at'        => Carbon::now()->subDays(8),
        ]);

        PipelineMovement::create([
            'master_project_id' => $master1->id,
            'from_department'   => 'mining',
            'to_department'     => 'environment',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => '5-Year Progressive Mining Plan approved by Joint Director of Geology and Mining.',
            'officer_id'        => $officerUser->id,
            'officer_name'      => $officerUser->name,
            'created_at'        => Carbon::now()->subDays(5),
        ]);

        PipelineMovement::create([
            'master_project_id' => $master1->id,
            'from_department'   => 'environment',
            'to_department'     => 'ppt',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => 'Form-1 & Pre-Feasibility Report scrutinized and scheduled for SEAC Technical Appraisal.',
            'officer_id'        => $officerUser->id,
            'officer_name'      => $officerUser->name,
            'created_at'        => Carbon::now()->subDays(3),
        ]);

        PipelineMovement::create([
            'master_project_id' => $master1->id,
            'from_department'   => 'ppt',
            'to_department'     => 'mining',
            'action_type'       => 'Returned_For_Correction',
            'priority'          => 'urgent',
            'mistake_category'  => 'Slope Stability & Safety Berm Hazard',
            'reason'            => 'SEAC Technical Appraisal committee noted excavation bench slope exceeds 45 degrees without berm terrace. Revise bench height to 6m with 6m berm terraces in Plate No. 4.',
            'rectification_notes' => 'Re-issue Progressive Quarrying Plate 4 with 6m berm terraces and blast vibration dampening study.',
            'officer_id'        => $adminUser->id,
            'officer_name'      => 'Dr. R. Ramanathan (SEAC Appraisal)',
            'created_at'        => Carbon::now()->subDays(1),
        ]);

        // =========================================================================
        // PROJECT 2: GTMS-2026-8091 (IN PROGRESS - MINING PLAN STAGE)
        // =========================================================================
        $cust2 = Customer::firstOrCreate(
            ['company_name' => 'Kongu Natural Stones & Minerals'],
            [
                'customer_name' => 'Thiru. M. Palanisamy',
                'mobile_num'    => '9842822002',
                'email'         => 'kongugranites@gmail.com',
                'gstin'         => '33BBBPK6789C1Z5',
                'branch_id'     => $branch->id,
                'created_by'    => $adminUser->id,
            ]
        );

        $lease2 = LeaseApplication::create([
            'application_no'    => 'LA-2026-8056',
            'customer_id'       => $cust2->id,
            'district_id'       => $dharmapuriDistrict->id,
            'mineral_id'        => $graniteMineral->id,
            'category_id'       => $leaseCat->id,
            'taluk'             => 'Pennagaram',
            'village'           => 'Eriyur',
            'area_extent_ha'    => '2.40.0',
            'branch_id'         => $branch->id,
            'status'            => 'approved',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(8),
        ]);

        $drone2 = DroneSurvey::create([
            'survey_no'         => 'DS-DRN-2026-8024',
            'customer_id'       => $cust2->id,
            'location'          => 'Eriyur Village, Pennagaram Taluk',
            'lease_area'        => 2.40,
            'branch_id'         => $branch->id,
            'survey_status'     => 'deliverables_ready',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(5),
        ]);

        $mining2 = MiningApplication::create([
            'application_no'    => 'MP-2026-8038',
            'lease_application_id' => $lease2->id,
            'customer_id'       => $cust2->id,
            'district_id'       => $dharmapuriDistrict->id,
            'mineral_id'        => $graniteMineral->id,
            'branch_id'         => $branch->id,
            'status'            => 'in_progress',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(2),
        ]);

        $master2 = MasterProject::create([
            'master_application_id' => 'GTMS-2026-8091',
            'customer_id'           => $cust2->id,
            'branch_id'             => $branch->id,
            'district_id'           => $dharmapuriDistrict->id,
            'taluk'                 => 'Pennagaram',
            'village'               => 'Eriyur',
            'survey_numbers'        => '88/2, 88/3',
            'area_extent_ha'        => 2.40,
            'mineral_name'          => 'Black Granite',
            'lease_application_id'  => $lease2->id,
            'drone_survey_id'       => $drone2->id,
            'mining_application_id' => $mining2->id,
            'current_stage'         => 'mining',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $adminUser->id,
            'created_at'            => Carbon::now()->subDays(8),
        ]);

        $lease2->update(['master_project_id' => $master2->id, 'common_id' => 'GTMS-2026-8091']);
        $drone2->update(['master_project_id' => $master2->id, 'common_id' => 'GTMS-2026-8091']);
        $mining2->update(['master_project_id' => $master2->id, 'common_id' => 'GTMS-2026-8091']);

        PipelineMovement::create([
            'master_project_id' => $master2->id,
            'from_department'   => 'lease',
            'to_department'     => 'survey_drone',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => 'Lease validated under TNMIR Rule 19(1). Forwarded for 3D drone volumetric survey.',
            'officer_id'        => $adminUser->id,
            'officer_name'      => $adminUser->name,
            'created_at'        => Carbon::now()->subDays(5),
        ]);

        PipelineMovement::create([
            'master_project_id' => $master2->id,
            'from_department'   => 'survey_drone',
            'to_department'     => 'mining',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => '3D contour contours and orthomosaic TIFF package uploaded. Ready for RQP mining scheme.',
            'officer_id'        => $officerUser->id,
            'officer_name'      => $officerUser->name,
            'created_at'        => Carbon::now()->subDays(2),
        ]);

        // =========================================================================
        // PROJECT 3: GTMS-2026-8104 (IN PROGRESS - ENVIRONMENT SEIAA STAGE)
        // =========================================================================
        $cust3 = Customer::firstOrCreate(
            ['company_name' => 'Nellai Earth Resources LLP'],
            [
                'customer_name' => 'Thiru. S. Muthuramalingam',
                'mobile_num'    => '9842933003',
                'email'         => 'nellaiearth@gmail.com',
                'gstin'         => '33CCCNM8901D1Z7',
                'branch_id'     => $branch->id,
                'created_by'    => $adminUser->id,
            ]
        );

        $lease3 = LeaseApplication::create([
            'application_no'    => 'LA-2026-8063',
            'customer_id'       => $cust3->id,
            'district_id'       => $tirunelveliDistrict->id,
            'mineral_id'        => $roughStoneMineral->id,
            'category_id'       => $leaseCat->id,
            'taluk'             => 'Nanguneri',
            'village'           => 'Parvathipuram',
            'area_extent_ha'    => '3.25.0',
            'branch_id'         => $branch->id,
            'status'            => 'approved',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(14),
        ]);

        $dgps3 = DgpsSurvey::create([
            'survey_no'         => 'DS-2026-8033',
            'field_book_no'     => 'FB-TNV-2026-8033',
            'customer_id'       => $cust3->id,
            'location'          => 'Parvathipuram Village, Nanguneri Taluk',
            'surveyed_area_ha'  => 3.25,
            'branch_id'         => $branch->id,
            'survey_status'     => 'completed',
            'report_status'     => 'verified',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(10),
        ]);

        $mining3 = MiningApplication::create([
            'application_no'    => 'MP-2026-8054',
            'lease_application_id' => $lease3->id,
            'customer_id'       => $cust3->id,
            'district_id'       => $tirunelveliDistrict->id,
            'mineral_id'        => $roughStoneMineral->id,
            'branch_id'         => $branch->id,
            'status'            => 'approved',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(6),
        ]);

        $env3 = EnvironmentProject::create([
            'project_code'      => 'ENV-2026-8021',
            'project_name'      => 'Parvathipuram EC Form 1 & Baseline EIA',
            'customer_id'       => $cust3->id,
            'district_id'       => $tirunelveliDistrict->id,
            'category'          => 'B2',
            'branch_id'         => $branch->id,
            'status'            => 'in_progress',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(2),
        ]);

        $master3 = MasterProject::create([
            'master_application_id'  => 'GTMS-2026-8104',
            'customer_id'            => $cust3->id,
            'branch_id'              => $branch->id,
            'district_id'            => $tirunelveliDistrict->id,
            'taluk'                  => 'Nanguneri',
            'village'                => 'Parvathipuram',
            'survey_numbers'         => '210/1, 210/4',
            'area_extent_ha'         => 3.25,
            'mineral_name'           => 'Rough Stone',
            'lease_application_id'   => $lease3->id,
            'dgps_survey_id'         => $dgps3->id,
            'mining_application_id'  => $mining3->id,
            'environment_project_id' => $env3->id,
            'current_stage'          => 'environment',
            'pipeline_status'        => 'in_progress',
            'created_by'             => $adminUser->id,
            'created_at'             => Carbon::now()->subDays(14),
        ]);

        $lease3->update(['master_project_id' => $master3->id, 'common_id' => 'GTMS-2026-8104']);
        $dgps3->update(['master_project_id' => $master3->id, 'common_id' => 'GTMS-2026-8104']);
        $mining3->update(['master_project_id' => $master3->id, 'common_id' => 'GTMS-2026-8104']);
        $env3->update(['master_project_id' => $master3->id, 'common_id' => 'GTMS-2026-8104']);

        PipelineMovement::create([
            'master_project_id' => $master3->id,
            'from_department'   => 'lease',
            'to_department'     => 'survey_dgps',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => 'Lease Challan MIMAS verified. Demarcation authorized.',
            'officer_id'        => $adminUser->id,
            'officer_name'      => $adminUser->name,
            'created_at'        => Carbon::now()->subDays(10),
        ]);

        PipelineMovement::create([
            'master_project_id' => $master3->id,
            'from_department'   => 'survey_dgps',
            'to_department'     => 'mining',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => 'DGPS survey field book closed and GPS points recorded.',
            'officer_id'        => $officerUser->id,
            'officer_name'      => $officerUser->name,
            'created_at'        => Carbon::now()->subDays(6),
        ]);

        PipelineMovement::create([
            'master_project_id' => $master3->id,
            'from_department'   => 'mining',
            'to_department'     => 'environment',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => 'Mining Plan approved by Deputy Director of Geology & Mining Tirunelveli.',
            'officer_id'        => $officerUser->id,
            'officer_name'      => $officerUser->name,
            'created_at'        => Carbon::now()->subDays(2),
        ]);

        // =========================================================================
        // PROJECT 4: GTMS-2026-8050 (COMPLETED - FULL LIFECYCLE CLEARED)
        // =========================================================================
        $cust4 = Customer::firstOrCreate(
            ['company_name' => 'Annamalaiyar Minerals Enterprise'],
            [
                'customer_name' => 'Thiru. V. Annamalai',
                'mobile_num'    => '9842044004',
                'email'         => 'annamalaiyarminerals@gmail.com',
                'gstin'         => '33DDDPA1234E1Z9',
                'branch_id'     => $branch->id,
                'created_by'    => $adminUser->id,
            ]
        );

        $lease4 = LeaseApplication::create([
            'application_no'    => 'LA-2026-8019',
            'customer_id'       => $cust4->id,
            'district_id'       => $tvmDistrict->id,
            'mineral_id'        => $graniteMineral->id,
            'category_id'       => $leaseCat->id,
            'taluk'             => 'Chengam',
            'village'           => 'Melpennathur',
            'area_extent_ha'    => '1.80.0',
            'branch_id'         => $branch->id,
            'status'            => 'approved',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(30),
        ]);

        $dgps4 = DgpsSurvey::create([
            'survey_no'         => 'DS-2026-8007',
            'field_book_no'     => 'FB-TVM-2026-8007',
            'customer_id'       => $cust4->id,
            'location'          => 'Melpennathur Village, Chengam Taluk',
            'surveyed_area_ha'  => 1.80,
            'branch_id'         => $branch->id,
            'survey_status'     => 'completed',
            'report_status'     => 'verified',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(25),
        ]);

        $mining4 = MiningApplication::create([
            'application_no'    => 'MP-2026-8012',
            'lease_application_id' => $lease4->id,
            'customer_id'       => $cust4->id,
            'district_id'       => $tvmDistrict->id,
            'mineral_id'        => $graniteMineral->id,
            'branch_id'         => $branch->id,
            'status'            => 'approved',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(18),
        ]);

        $env4 = EnvironmentProject::create([
            'project_code'      => 'ENV-2026-8009',
            'project_name'      => 'Melpennathur Granite EC Cleared',
            'customer_id'       => $cust4->id,
            'district_id'       => $tvmDistrict->id,
            'category'          => 'B2',
            'branch_id'         => $branch->id,
            'status'            => 'approved',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(10),
        ]);

        $ppt4 = PptApplication::create([
            'application_no'    => 'PPT-2026-8004',
            'project_name'      => 'SEAC Presentation — Annamalaiyar Granite',
            'customer_id'       => $cust4->id,
            'district_id'       => $tvmDistrict->id,
            'mineral_id'        => $graniteMineral->id,
            'branch_id'         => $branch->id,
            'status'            => 'completed',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subDays(4),
        ]);

        $master4 = MasterProject::create([
            'master_application_id'  => 'GTMS-2026-8050',
            'customer_id'            => $cust4->id,
            'branch_id'              => $branch->id,
            'district_id'            => $tvmDistrict->id,
            'taluk'                  => 'Chengam',
            'village'                => 'Melpennathur',
            'survey_numbers'         => '55/1, 55/2A',
            'area_extent_ha'         => 1.80,
            'mineral_name'           => 'Black Granite',
            'lease_application_id'   => $lease4->id,
            'dgps_survey_id'         => $dgps4->id,
            'mining_application_id'  => $mining4->id,
            'environment_project_id' => $env4->id,
            'ppt_application_id'     => $ppt4->id,
            'current_stage'          => 'completed',
            'pipeline_status'        => 'completed',
            'created_by'             => $adminUser->id,
            'created_at'             => Carbon::now()->subDays(30),
        ]);

        $lease4->update(['master_project_id' => $master4->id, 'common_id' => 'GTMS-2026-8050']);
        $dgps4->update(['master_project_id' => $master4->id, 'common_id' => 'GTMS-2026-8050']);
        $mining4->update(['master_project_id' => $master4->id, 'common_id' => 'GTMS-2026-8050']);
        $env4->update(['master_project_id' => $master4->id, 'common_id' => 'GTMS-2026-8050']);
        $ppt4->update(['master_project_id' => $master4->id, 'common_id' => 'GTMS-2026-8050']);

        PipelineMovement::create([
            'master_project_id' => $master4->id,
            'from_department'   => 'lease',
            'to_department'     => 'survey_dgps',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => 'Lease Application approved and registered.',
            'officer_id'        => $adminUser->id,
            'officer_name'      => $adminUser->name,
            'created_at'        => Carbon::now()->subDays(25),
        ]);

        PipelineMovement::create([
            'master_project_id' => $master4->id,
            'from_department'   => 'survey_dgps',
            'to_department'     => 'mining',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => 'DGPS demarcation completed.',
            'officer_id'        => $officerUser->id,
            'officer_name'      => $officerUser->name,
            'created_at'        => Carbon::now()->subDays(18),
        ]);

        PipelineMovement::create([
            'master_project_id' => $master4->id,
            'from_department'   => 'mining',
            'to_department'     => 'environment',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => 'Mining Plan approved by Geology Department.',
            'officer_id'        => $officerUser->id,
            'officer_name'      => $officerUser->name,
            'created_at'        => Carbon::now()->subDays(10),
        ]);

        PipelineMovement::create([
            'master_project_id' => $master4->id,
            'from_department'   => 'environment',
            'to_department'     => 'ppt',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => 'SEIAA appraisal documentation ready.',
            'officer_id'        => $officerUser->id,
            'officer_name'      => $officerUser->name,
            'created_at'        => Carbon::now()->subDays(4),
        ]);

        PipelineMovement::create([
            'master_project_id' => $master4->id,
            'from_department'   => 'ppt',
            'to_department'     => 'completed',
            'action_type'       => 'Forward_Stage',
            'priority'          => 'normal',
            'reason'            => 'Environmental Clearance issued by SEIAA. Official Quarry Lease Grant Order issued.',
            'officer_id'        => $adminUser->id,
            'officer_name'      => $adminUser->name,
            'created_at'        => Carbon::now()->subDays(1),
        ]);

        // =========================================================================
        // PROJECT 5: GTMS-2026-8112 (FRESH INTAKE - LEASE STAGE)
        // =========================================================================
        $cust5 = Customer::firstOrCreate(
            ['company_name' => 'Meenakshi Blue Metal Traders'],
            [
                'customer_name' => 'Thiru. T. Karuppasamy',
                'mobile_num'    => '9842155005',
                'email'         => 'meenakshibluemetal@gmail.com',
                'gstin'         => '33EEEMK5678F1Z1',
                'branch_id'     => $branch->id,
                'created_by'    => $adminUser->id,
            ]
        );

        $lease5 = LeaseApplication::create([
            'application_no'    => 'LA-2026-8088',
            'customer_id'       => $cust5->id,
            'district_id'       => $maduraiDistrict->id,
            'mineral_id'        => $roughStoneMineral->id,
            'category_id'       => $leaseCat->id,
            'taluk'             => 'Melur',
            'village'           => 'Kottampatti',
            'area_extent_ha'    => '1.20.0',
            'branch_id'         => $branch->id,
            'status'            => 'under_scrutiny',
            'payment_status'    => 'paid',
            'created_by'        => $officerUser->id,
            'created_at'        => Carbon::now()->subHours(6),
        ]);

        $master5 = MasterProject::create([
            'master_application_id' => 'GTMS-2026-8112',
            'customer_id'           => $cust5->id,
            'branch_id'             => $branch->id,
            'district_id'           => $maduraiDistrict->id,
            'taluk'                 => 'Melur',
            'village'               => 'Kottampatti',
            'survey_numbers'        => '305/1B',
            'area_extent_ha'        => 1.20,
            'mineral_name'          => 'Rough Stone',
            'lease_application_id'  => $lease5->id,
            'current_stage'         => 'lease',
            'pipeline_status'       => 'in_progress',
            'created_by'            => $adminUser->id,
            'created_at'            => Carbon::now()->subHours(6),
        ]);

        $lease5->update(['master_project_id' => $master5->id, 'common_id' => 'GTMS-2026-8112']);
    }
}

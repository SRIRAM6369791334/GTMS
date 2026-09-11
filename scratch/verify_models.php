<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\District;
use App\Models\Mineral;
use App\Models\LeaseCategory;
use App\Models\PlanType;
use App\Models\Customer;
use App\Models\LeaseApplication;
use App\Models\MiningApplication;
use App\Models\EnvironmentProject;
use App\Models\EcCertificate;
use App\Models\PptApplication;
use App\Models\DgpsSurvey;
use App\Models\DroneSurvey;
use App\Models\MineralStockpile;

try {
    DB::beginTransaction();

    $d = District::first();
    $m = Mineral::first();
    $cat = LeaseCategory::first();
    $plan = PlanType::first();

    echo "Loaded Masters: District=" . $d->district_name . ", Mineral=" . $m->mineral_name . PHP_EOL;

    // 1. Customer
    $customer = Customer::create([
        'customer_name' => 'Kovai Blue Metal Minerals',
        'company_name' => 'Kovai Granites Pvt Ltd',
        'mobile_num' => '9842112233',
        'district_id' => $d->id,
        'mineral_id' => $m->id,
        'pan' => 'ABCDE1234F',
        'gstin' => '33ABCDE1234F1Z5',
        'status' => 1,
    ]);
    echo "1. Customer Created: ID " . $customer->id . " - " . $customer->customer_name . PHP_EOL;

    // 2. Lease Application & Survey Number
    $lease = $customer->leaseApplications()->create([
        'application_no' => 'LA-COI-2026-0001',
        'district_id' => $d->id,
        'category_id' => $cat->id,
        'mineral_id' => $m->id,
        'taluk' => 'Madukkarai',
        'village' => 'Ettimadai',
        'area_extent_ha' => 4.85,
        'current_step' => 2,
        'status' => 'draft',
    ]);
    $surveyNo = $lease->surveyNumbers()->create([
        'survey_no' => '245/1B',
        'sub_division' => '1',
        'extent_ha' => 4.85,
    ]);
    echo "2. Lease Created: " . $lease->application_no . " with Survey No: " . $surveyNo->survey_no . PHP_EOL;

    // 3. Mining Application & Boundary Points & Production Schedule
    $mining = $customer->miningApplications()->create([
        'application_no' => 'MP-COI-2026-0001',
        'lease_application_id' => $lease->id,
        'district_id' => $d->id,
        'mineral_id' => $m->id,
        'plan_type_id' => $plan->id,
        'stage' => '6.1',
        'status' => 'draft',
        'area_extent_ha' => 4.85,
    ]);
    $p1 = $mining->boundaryPoints()->create([
        'pillar_id' => 'P1',
        'latitude' => 10.90123456,
        'longitude' => 76.90123456,
        'elevation' => 310.50,
    ]);
    $sched = $mining->productionSchedules()->create([
        'year_number' => 1,
        'production_target' => 75000.00,
        'waste_removal' => 15000.00,
    ]);
    echo "3. Mining Plan Created: " . $mining->application_no . " with Pillar " . $p1->pillar_id . " and Year 1 Target: " . $sched->production_target . PHP_EOL;

    // 4. Environment Project & EC Certificate
    $env = EnvironmentProject::create([
        'project_code' => 'ENV-B2-2026-0001',
        'customer_id' => $customer->id,
        'mining_application_id' => $mining->id,
        'lease_application_id' => $lease->id,
        'category' => 'B2',
        'project_name' => 'Kovai Rough Stone Quarry B2 Project',
        'district_id' => $d->id,
        'status' => 'draft',
    ]);
    $ec = $env->ecCertificates()->create([
        'ec_ref_no' => 'EC-SEIAA-TN-2026-999',
        'customer_id' => $customer->id,
        'lease_application_id' => $lease->id,
        'applicant_name' => $customer->customer_name,
        'issue_date' => now()->toDateString(),
        'expiry_date' => now()->addYears(5)->toDateString(),
        'validity_years' => 5,
        'communication_type' => 'Grant',
        'status' => 'active',
    ]);
    echo "4. Environment Project Created: " . $env->project_code . " with EC Ref: " . $ec->ec_ref_no . PHP_EOL;

    // 5. PPT Application & Agenda
    $ppt = PptApplication::create([
        'application_no' => 'PPT-2026-0001',
        'customer_id' => $customer->id,
        'environment_project_id' => $env->id,
        'project_name' => 'SEAC PPT Presentation for Kovai Quarry',
        'district_id' => $d->id,
        'mineral_id' => $m->id,
        'status' => 'draft',
    ]);
    $agenda = $ppt->agendas()->create([
        'committee_type' => 'SEAC',
        'meeting_no' => 'M-412',
        'item_no' => 'Item-18',
        'meeting_date' => now()->addDays(14)->toDateString(),
        'outcome' => 'Recommended',
    ]);
    echo "5. PPT Created: " . $ppt->application_no . " with Meeting " . $agenda->meeting_no . PHP_EOL;

    // 6. DGPS Survey & Points
    $dgps = $customer->dgpsSurveys()->create([
        'survey_no' => 'DGPS-2026-0001',
        'lease_application_id' => $lease->id,
        'mining_application_id' => $mining->id,
        'lease_area_ha' => 4.85,
        'surveyed_area_ha' => 4.83,
        'area_discrepancy_ha' => -0.02,
        'survey_status' => 'completed',
        'report_status' => 'verified',
    ]);
    $gcp = $dgps->points()->create([
        'pillar_no' => 'GCP-1',
        'latitude' => 10.90123456,
        'longitude' => 76.90123456,
        'elevation' => 310.50,
    ]);
    echo "6. DGPS Survey Created: " . $dgps->survey_no . " with Surveyed Area: " . $dgps->surveyed_area_ha . " Ha" . PHP_EOL;

    // 7. Drone Survey
    $drone = $customer->droneSurveys()->create([
        'survey_no' => 'DRONE-2026-0001',
        'lease_application_id' => $lease->id,
        'mining_application_id' => $mining->id,
        'lease_area' => 4.85,
        'extracted_volume_cbm' => 28400.75,
        'survey_status' => 'deliverables_ready',
    ]);
    echo "7. Drone Survey Created: " . $drone->survey_no . " with Extracted Volume: " . $drone->extracted_volume_cbm . " CBM" . PHP_EOL;

    // 8. Mineral Stockpile, Stock In, Dispatches
    $stockpile = MineralStockpile::create([
        'quarry_customer_id' => $customer->id,
        'lease_application_id' => $lease->id,
        'mineral_id' => $m->id,
        'annual_permitted_quota' => 75000.00,
        'current_stock_cbm' => 20000.00,
        'total_dispatched_cbm' => 8400.00,
        'unit' => 'CBM',
        'status' => 1,
    ]);
    $entry = $stockpile->entries()->create([
        'entry_date' => now()->toDateString(),
        'quantity' => 20000.00,
        'source_type' => 'quarry_extraction',
        'remarks' => 'Monthly extraction audit',
    ]);
    $dispatch = $stockpile->dispatches()->create([
        'dispatch_date' => now(),
        'quantity' => 8400.00,
        'vehicle_number' => 'TN-38-BZ-9090',
        'seigniorage_fee_inr' => 42000.00,
        'challan_no' => 'CHL-TN-2026-0042',
        'status' => 'dispatched',
    ]);
    echo "8. Mineral Stockpile Created: " . $stockpile->current_stock_cbm . " CBM available, Dispatched: " . $dispatch->quantity . " CBM, Seigniorage: Rs." . $dispatch->seigniorage_fee_inr . PHP_EOL;

    // Verify reverse relationships
    echo "--- REVERSE RELATIONSHIP VERIFICATION ---" . PHP_EOL;
    echo "Customer -> Lease Applications count: " . $customer->leaseApplications()->count() . PHP_EOL;
    echo "Customer -> Mining Applications count: " . $customer->miningApplications()->count() . PHP_EOL;
    echo "Customer -> DGPS Surveys count: " . $customer->dgpsSurveys()->count() . PHP_EOL;
    echo "Customer -> Drone Surveys count: " . $customer->droneSurveys()->count() . PHP_EOL;
    echo "Customer -> Stockpiles count: " . $customer->stockpiles()->count() . PHP_EOL;
    echo "Lease -> Survey Numbers count: " . $lease->surveyNumbers()->count() . PHP_EOL;
    echo "Mining -> Boundary Points count: " . $mining->boundaryPoints()->count() . PHP_EOL;
    echo "Environment Project -> EC Certificates count: " . $env->ecCertificates()->count() . PHP_EOL;
    echo "PPT -> Agendas count: " . $ppt->agendas()->count() . PHP_EOL;
    echo "Stockpile -> Entries count: " . $stockpile->entries()->count() . PHP_EOL;
    echo "Stockpile -> Dispatches count: " . $stockpile->dispatches()->count() . PHP_EOL;

    // Rollback test data to keep database clean
    DB::rollBack();
    echo "SUCCESS: ALL RELATIONSHIPS VERIFIED AND TRANSACTION ROLLED BACK CLEANLY!" . PHP_EOL;

} catch (\Exception $e) {
    DB::rollBack();
    echo "ERROR: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}

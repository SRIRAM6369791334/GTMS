<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\District;
use App\Models\DocumentField;
use App\Models\Folder;
use App\Models\LeaseApplication;
use App\Models\LeaseDocument;
use App\Models\Mineral;
use App\Models\MiningApplication;
use App\Models\MiningDocument;
use App\Models\NatureOfWork;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MiningPlanTransitionTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }
        $this->user = $user;
        $this->actingAs($this->user);
    }

    /**
     * Helper to prepare a valid test lease application
     */
    protected function createTestLease(): LeaseApplication
    {
        $district = District::first() ?? District::create(['name' => 'Salem', 'status' => 1]);
        $customer = Customer::first() ?? Customer::create([
            'customer_name' => 'Sri Bala Traders',
            'company_name'  => 'Sri Bala Traders',
            'mimas_no'      => 'TN-MMS-SLM-001',
            'mobile_num'    => '9876543210',
            'email'         => 'balatraders@gmail.com',
            'pan'           => 'ABCDE1234F',
            'aadhaar_no'    => '9876-5432-1012',
            'status'        => 'active',
        ]);

        $mineral = Mineral::first() ?? Mineral::create(['name' => 'Rough Stone', 'status' => 1]);

        $year = date('Y');
        $randomSeq = (string)rand(10000, 99999) . rand(100, 999);
        $appNo = "LA-{$year}-{$randomSeq}";
        $commonId = "GTMS-{$year}-{$randomSeq}";

        $categoryId = DB::table('lease_categories')->value('id') ?? 1;

        $lease = LeaseApplication::create([
            'common_id'                 => $commonId,
            'application_no'            => $appNo,
            'customer_id'               => $customer->id,
            'district_id'               => $district->id,
            'category_id'               => $categoryId,
            'mineral_id'                => $mineral->id,
            'taluk'                     => 'Omalur',
            'village'                   => 'Karuppur',
            'sf_no'                     => 'SF.No 101/1, 101/2',
            'area_extent_ha'            => 2.50,
            'lease_period_years'        => 5,
            'contact_person'            => 'R. Balasubramanian',
            'contact_mobile'            => '9876543210',
            'secondary_contact_person'  => 'S. Murugan',
            'secondary_contact_mobile'  => '9876543211',
            'status'                    => 'approved',
            'current_step'              => 6,
            'branch_id'                 => 1,
            'created_by'                => $this->user->id,
        ]);

        // Attach mineral pivot
        $lease->minerals()->sync([$mineral->id]);

        // Create a dummy document file on disk
        $dummyDir = public_path("uploads/lease_applications/{$appNo}");
        if (!file_exists($dummyDir)) {
            mkdir($dummyDir, 0777, true);
        }
        $dummyFile = $dummyDir . "/1_land_document.pdf";
        file_put_contents($dummyFile, "%PDF-1.4 Mock Land Document Content for Testing");

        // Attach LeaseDocument
        LeaseDocument::create([
            'lease_application_id' => $lease->id,
            'folder_id'            => 7, // Documents folder
            'document_field_id'    => null,
            'document_name'        => '1. Land Document',
            'file_name'            => '1_land_document.pdf',
            'file_path'            => "uploads/lease_applications/{$appNo}/1_land_document.pdf",
            'file_type'            => 'application/pdf',
            'file_size'            => 1024,
            'status'               => 'validated',
            'uploaded_by'          => $this->user->id,
            'uploaded_at'          => now(),
        ]);

        return $lease;
    }

    /**
     * Test 1: Transition approved lease application to mining plan
     */
    public function test_approved_lease_can_transition_to_mining_plan(): void
    {
        $lease = $this->createTestLease();

        $response = $this->post(route('application.moveToMining', $lease->id));

        $miningApp = MiningApplication::where('lease_application_id', $lease->id)->first();
        $this->assertNotNull($miningApp, "Mining Application should have been created for lease #{$lease->id}");

        $response->assertRedirect('/process?id=' . $miningApp->id);

        $this->assertEquals($lease->common_id, $miningApp->common_id);
        $this->assertEquals($lease->customer_id, $miningApp->customer_id);
        $this->assertEquals($lease->district_id, $miningApp->district_id);
        $this->assertEquals($lease->taluk, $miningApp->taluk);
        $this->assertEquals($lease->village, $miningApp->village);
        $this->assertEquals($lease->area_extent_ha, $miningApp->area_extent_ha);

        // Check document cloning
        $clonedDoc = MiningDocument::where('mining_application_id', $miningApp->id)
            ->where('document_name', '1. Land Document')
            ->first();

        $this->assertNotNull($clonedDoc, "Document should have been cloned to Mining Documents");
        $this->assertFileExists(public_path($clonedDoc->file_path));

        // Clean up mock files
        if ($clonedDoc && file_exists(public_path($clonedDoc->file_path))) {
            @unlink(public_path($clonedDoc->file_path));
        }
    }

    /**
     * Test 2: Moving lease twice is idempotent and redirects safely without duplicating records
     */
    public function test_moving_lease_twice_is_idempotent_and_redirects_safely(): void
    {
        $lease = $this->createTestLease();

        // First transition
        $this->post(route('application.moveToMining', $lease->id));
        $countAfterFirst = MiningApplication::where('lease_application_id', $lease->id)->count();
        $this->assertEquals(1, $countAfterFirst);

        $firstMining = MiningApplication::where('lease_application_id', $lease->id)->first();

        // Second transition attempt (Idempotency)
        $response = $this->post(route('application.moveToMining', $lease->id));

        $response->assertRedirect('/process?id=' . $firstMining->id);
        $response->assertSessionHas('info');

        $countAfterSecond = MiningApplication::where('lease_application_id', $lease->id)->count();
        $this->assertEquals(1, $countAfterSecond, "Mining Application must not be duplicated");
    }

    /**
     * Test 3: Universal Common ID (GTMS-YYYY-XXXX) is preserved across lease and mining records
     */
    public function test_universal_common_id_is_preserved_across_lease_and_mining(): void
    {
        $lease = $this->createTestLease();
        $this->post(route('application.moveToMining', $lease->id));

        $miningApp = MiningApplication::where('lease_application_id', $lease->id)->first();

        $this->assertNotNull($miningApp);
        $this->assertMatchesRegularExpression('/^GTMS-\d{4}-\d{4}/', $miningApp->common_id);
        $this->assertEquals($lease->common_id, $miningApp->common_id);
    }

    /**
     * Test 4: Resuming mining application prefills and updates existing record without duplicate documents
     */
    public function test_resuming_mining_application_prefills_and_updates_without_duplicates(): void
    {
        $lease = $this->createTestLease();
        $this->post(route('application.moveToMining', $lease->id));
        $miningApp = MiningApplication::where('lease_application_id', $lease->id)->first();

        // 1. GET resume page renders successfully with prefilled customer data
        $resumeResponse = $this->get(route('newapplication', ['resume' => $miningApp->id]));
        $resumeResponse->assertStatus(200);
        $resumeResponse->assertSee($miningApp->application_no);
        $resumeResponse->assertSee('Resuming Application');

        // 2. Submit update via store endpoint
        $nowId = NatureOfWork::first()->id ?? 1;
        $mineralId = Mineral::first()->id ?? 1;
        $districtId = District::first()->id ?? 1;

        $docCountBefore = MiningDocument::where('mining_application_id', $miningApp->id)->count();

        $postData = [
            'mining_app_id'       => $miningApp->id,
            'nature_of_work_id'   => $nowId,
            'applicant_type_id'   => 1,
            'district_id'         => $districtId,
            'plan_type_id'        => 1,
            'mineral_ids'         => [$mineralId],
            'customer_id'         => $miningApp->customer_id,
            'client_name'         => 'Updated Client Name',
            'company_name'        => 'Updated Company Name',
            'mobile_num'          => '9876543210',
            'taluk'               => 'Omalur Updated',
            'village'             => 'Karuppur Updated',
            'survey_numbers_text' => 'SF.No 101/1A',
            'area_extent_ha'      => 3.25,
        ];

        $submitResponse = $this->post(route('newapplication.store'), $postData);
        $submitResponse->assertRedirect(route('projectfolder', ['id' => $miningApp->id]));

        // Refresh and verify updated values
        $miningApp->refresh();
        $this->assertEquals('Omalur Updated', $miningApp->taluk);
        $this->assertEquals('Karuppur Updated', $miningApp->village);
        $this->assertEquals(3.25, $miningApp->area_extent_ha);

        // Verify document records were not duplicated
        $docCountAfter = MiningDocument::where('mining_application_id', $miningApp->id)->count();
        $this->assertEquals($docCountBefore, $docCountAfter, "Submitting update must not duplicate document records");
    }

    /**
     * Test 5: All 5 mining routes render HTTP 200
     */
    public function test_all_mining_routes_render_http_200(): void
    {
        $lease = $this->createTestLease();
        $this->post(route('application.moveToMining', $lease->id));
        $miningApp = MiningApplication::where('lease_application_id', $lease->id)->first();

        // Route 1: Mining Plan Index
        $r1 = $this->get(route('miningplan.index'));
        $r1->assertStatus(200);

        // Route 2: New Application Intake
        $r2 = $this->get(route('newapplication'));
        $r2->assertStatus(200);

        // Route 3: Project Folder Dossier
        $r3 = $this->get(route('projectfolder', ['id' => $miningApp->id]));
        $r3->assertStatus(200);

        // Route 4: Document Upload & Manage
        $r4 = $this->get(route('document', ['id' => $miningApp->id]));
        $r4->assertStatus(200);

        // Route 5: 6-Stage Process Flow
        $r5 = $this->get(route('process', ['id' => $miningApp->id]));
        $r5->assertStatus(200);
        $r5->assertSee('Resume / Edit Application');
    }

    /**
     * Test 6: Zero Tamil Unicode characters across the entire codebase
     */
    public function test_codebase_contains_zero_tamil_characters(): void
    {
        $pathsToCheck = [
            app_path(),
            resource_path('views'),
            base_path('routes'),
            database_path('migrations'),
            database_path('seeders'),
        ];

        $tamilPattern = '/[\x{0B80}-\x{0BFF}]/u';
        $violations = [];

        foreach ($pathsToCheck as $baseDir) {
            if (!is_dir($baseDir)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($baseDir, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && in_array($file->getExtension(), ['php', 'blade', 'js', 'css'])) {
                    $content = file_get_contents($file->getRealPath());
                    if (preg_match($tamilPattern, $content)) {
                        $violations[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getRealPath());
                    }
                }
            }
        }

        $this->assertEmpty($violations, 'Tamil Unicode characters detected in files: ' . implode(', ', $violations));
    }
}

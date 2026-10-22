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
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MiningPlanTransitionTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        $this->user = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->first()
            ?: User::where('role_id', 1)->first()
            ?: User::first();

        if ($this->user && !$this->user->hasRole(['Admin', 'Super Admin'])) {
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $this->user->assignRole($adminRole);
        }
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

        $response->assertRedirect(route('viewapplication', ['id' => $lease->id]));
        $response->assertSessionHas('mining_transition_success');

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
     * Test 1b: Unapproved lease application (under_scrutiny or validated) cannot transition to mining plan
     */
    public function test_unapproved_lease_cannot_transition_to_mining_plan(): void
    {
        $lease = $this->createTestLease();
        $lease->update(['status' => 'under_scrutiny']);

        // Attempting to transition unapproved lease via regular POST request should be blocked
        $response = $this->from('/viewapplication?id=' . $lease->id)->post(route('application.moveToMining', $lease->id));
        $response->assertSessionHas('error');
        $this->assertNull(MiningApplication::where('lease_application_id', $lease->id)->first());

        // Attempting to transition via AJAX / JSON request should return HTTP 422
        $ajaxResponse = $this->postJson(route('application.moveToMining', $lease->id));
        $ajaxResponse->assertStatus(422);
        $ajaxResponse->assertJsonFragment([
            'status' => 0,
        ]);
        $this->assertNull(MiningApplication::where('lease_application_id', $lease->id)->first());
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

        $response->assertRedirect(route('viewapplication', ['id' => $lease->id]));
        $response->assertSessionHas('info');

        // Also test AJAX idempotency response stays on viewapplication
        $ajaxResponse = $this->postJson(route('application.moveToMining', $lease->id));
        $ajaxResponse->assertOk();
        $ajaxResponse->assertJsonFragment([
            'status'        => 1,
            'already_moved' => true,
            'redirect'      => route('viewapplication', ['id' => $lease->id]),
        ]);

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
     * Test 6: Draft mining application shows ONLY resume button, and hides operational buttons until submitted
     */
    public function test_draft_mining_application_shows_only_resume_button_and_hides_operational_buttons(): void
    {
        $lease = $this->createTestLease();
        $this->post(route('application.moveToMining', $lease->id));
        $miningApp = MiningApplication::where('lease_application_id', $lease->id)->first();

        $this->assertEquals('draft', $miningApp->status);

        // 1. In index view while draft: ONLY Resume button must be visible, operational buttons hidden
        $indexResponse = $this->get(route('miningplan.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee(route('newapplication', ['resume' => $miningApp->id]));
        $indexResponse->assertDontSee(route('projectfolder', ['id' => $miningApp->id]));
        $indexResponse->assertDontSee(route('document', ['id' => $miningApp->id]));
        $indexResponse->assertDontSee(route('process', ['id' => $miningApp->id]));

        // 2. Resume & submit the application
        $nowId = NatureOfWork::first()->id ?? 1;
        $mineralId = Mineral::first()->id ?? 1;
        $districtId = District::first()->id ?? 1;

        $postData = [
            'mining_app_id'       => $miningApp->id,
            'nature_of_work_id'   => $nowId,
            'applicant_type_id'   => 1,
            'district_id'         => $districtId,
            'plan_type_id'        => 1,
            'mineral_ids'         => [$mineralId],
            'customer_id'         => $miningApp->customer_id,
            'client_name'         => 'Test Client',
            'company_name'        => 'Test Company',
            'mobile_num'          => '9876543210',
            'taluk'               => 'Test Taluk',
            'village'             => 'Test Village',
            'survey_numbers_text' => 'SF.No 123',
            'area_extent_ha'      => 2.50,
        ];

        $submitResponse = $this->post(route('newapplication.store'), $postData);
        $submitResponse->assertRedirect(route('projectfolder', ['id' => $miningApp->id]));

        $miningApp->refresh();
        $this->assertEquals('scrutiny', $miningApp->status);
        $this->assertEquals('6.2', $miningApp->stage);

        // 3. In index view after submit: Resume button is hidden, 3 operational buttons are visible
        $indexResponseAfter = $this->get(route('miningplan.index'));
        $indexResponseAfter->assertStatus(200);
        $indexResponseAfter->assertDontSee(route('newapplication', ['resume' => $miningApp->id]));
        $indexResponseAfter->assertSee(route('projectfolder', ['id' => $miningApp->id]));
        $indexResponseAfter->assertSee(route('document', ['id' => $miningApp->id]));
        $indexResponseAfter->assertSee(route('process', ['id' => $miningApp->id]));
    }

    /**
     * Test 7: Nature of Work "Others" saves, validates, and prefills custom scope name
     */
    public function test_nature_of_work_others_saves_validates_and_prefills_custom_scope_name(): void
    {
        $lease = $this->createTestLease();
        $this->post(route('application.moveToMining', $lease->id));
        $miningApp = MiningApplication::where('lease_application_id', $lease->id)->first();

        // 1. Resume page renders custom scope input container
        $resumeGet = $this->get(route('newapplication', ['resume' => $miningApp->id]));
        $resumeGet->assertStatus(200);
        $resumeGet->assertSee('other_nature_of_work_box_mining');
        $resumeGet->assertSee('Specify Nature of Work Scope');

        // 2. Others nature of work ID
        $othersNow = NatureOfWork::where('name', 'Others')->first();
        if (!$othersNow) {
            $othersNow = NatureOfWork::create(['id' => 8, 'name' => 'Others', 'status' => 1]);
        }
        $districtId = District::first()->id ?? 1;

        // 3. Validation fails when Others is selected without custom scope name
        $invalidPostData = [
            'mining_app_id'              => $miningApp->id,
            'nature_of_work_id'          => $othersNow->id,
            'other_nature_of_work_name'  => '',
            'applicant_type_id'          => 1,
            'district_id'                => $districtId,
            'customer_id'                => $miningApp->customer_id,
            'client_name'                => 'Test Client',
            'company_name'               => 'Test Company',
            'mobile_num'                 => '9876543210',
            'taluk'                      => 'Test Taluk',
            'village'                    => 'Test Village',
            'survey_numbers_text'        => 'SF.No 123',
            'area_extent_ha'             => 2.50,
        ];

        $invalidResponse = $this->post(route('newapplication.store'), $invalidPostData);
        $invalidResponse->assertSessionHasErrors('other_nature_of_work_name');

        // 4. Valid submission with custom scope name succeeds
        $validPostData = $invalidPostData;
        $validPostData['other_nature_of_work_name'] = 'Mineral Exploration & Processing';

        $validResponse = $this->post(route('newapplication.store'), $validPostData);
        $validResponse->assertRedirect(route('projectfolder', ['id' => $miningApp->id]));

        $miningApp->refresh();
        $this->assertEquals($othersNow->id, $miningApp->nature_of_work_id);
        $this->assertEquals('Mineral Exploration & Processing', $miningApp->other_nature_of_work_name);

        // 5. Resume page prefills the custom scope name
        $resumeAgain = $this->get(route('newapplication', ['resume' => $miningApp->id]));
        $resumeAgain->assertStatus(200);
        $resumeAgain->assertSee('Mineral Exploration &amp; Processing', false);

        // 6. Project folder renders custom scope name in badge
        $dossierGet = $this->get(route('projectfolder', ['id' => $miningApp->id]));
        $dossierGet->assertStatus(200);
        $dossierGet->assertSee('Others (Mineral Exploration &amp; Processing)', false);

        // 7. Mining directory renders custom scope name
        $indexGet = $this->get(route('miningplan.index'));
        $indexGet->assertStatus(200);
        $indexGet->assertSee('Others (Mineral Exploration &amp; Processing)', false);
    }

    /**
     * Test 8: Current Step Persistence & Reload Synchronization
     */
    public function test_mining_wizard_preserves_current_step_on_autosave_and_reload(): void
    {
        $this->actingAs($this->user);

        // 1. Create a draft mining application via helper
        $lease = $this->createTestLease();
        $this->post(route('application.moveToMining', $lease->id));
        $miningApp = MiningApplication::where('lease_application_id', $lease->id)->first();
        $this->assertNotNull($miningApp);

        // 2. Perform auto-save to advance to Step 2
        $autoSaveResponse = $this->postJson(route('newapplication.autosave'), [
            'mining_app_id'     => $miningApp->id,
            'current_step'      => 2,
            'nature_of_work_id' => $miningApp->nature_of_work_id,
            'client_name'       => 'Test Applicant',
            'company_name'      => 'Test Granite Corp',
            'mobile_num'        => '9876543210',
            'applicant_type_id' => $miningApp->applicant_type_id,
            'district_id'       => $miningApp->district_id,
        ]);

        $autoSaveResponse->assertStatus(200);
        $autoSaveResponse->assertJson([
            'status'        => 1,
            'mining_app_id' => $miningApp->id,
            'current_step'  => 2,
        ]);

        // 3. Verify DB record has current_step = 2
        $miningApp->refresh();
        $this->assertEquals(2, $miningApp->current_step);

        // 4. Reload page via /newapplication?resume=X without explicit step param -> must prefill step 2
        $resumeGet = $this->get(route('newapplication', ['resume' => $miningApp->id]));
        $resumeGet->assertStatus(200);
        $resumeGet->assertSee('value="2"', false);
        $resumeGet->assertSee('id="field_current_step"', false);

        // 5. Reload page with explicit step parameter -> must honor requested step
        $resumeWithStep3 = $this->get(route('newapplication', ['resume' => $miningApp->id, 'step' => 3]));
        $resumeWithStep3->assertStatus(200);
        $resumeWithStep3->assertSee('name="current_step" id="field_current_step" value="3"', false);

        // 6. Mining directory index resume button contains current_step in URL
        $indexGet = $this->get(route('miningplan.index'));
        $indexGet->assertStatus(200);
        $indexGet->assertSee('resume=' . $miningApp->id . '&amp;step=2', false);
    }

    /**
     * Test 9: Zero Tamil Unicode characters across the entire codebase
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

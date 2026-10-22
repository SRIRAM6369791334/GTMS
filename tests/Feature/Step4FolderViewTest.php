<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Step4FolderViewTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'application.create', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'application.view', 'guard_name' => 'web']);

        $this->user = User::factory()->create();
        $this->user->givePermissionTo(['application.create', 'application.view']);
    }

    /** @test */
    public function step4_renders_successfully_with_stepper_and_all_three_folders()
    {
        $response = $this->actingAs($this->user)->get(route('step4'));

        $response->assertStatus(200);
        $response->assertSee('id="gtms-step4-page"', false);
        $response->assertSee('Regulatory Document Folders');
        $response->assertSee('1. Statutory Documents');
        $response->assertSee('2. Lease Application');
        $response->assertSee('3. Mining &amp; Survey Plans', false);
        $response->assertSee('Dossier Readiness');
        $response->assertSee('Open Documents');
        $response->assertSee('Back to Step 3');
    }

    /** @test */
    public function step4_calculates_and_displays_folder_counts_and_percentages_correctly()
    {
        // Mock session with 2 statutory docs, 1 lease doc, 1 survey plan
        $mockDraft = [
            'application_id' => 999,
            'step3' => [
                'category_id' => 1,
            ],
            'uploaded_docs' => [
                1 => ['folder_id' => 7, 'file_name' => 'patta.pdf'],
                2 => ['folder_id' => 7, 'file_name' => 'fmb.pdf'],
                10 => ['folder_id' => 8, 'file_name' => 'affidavit.pdf'],
                17 => ['folder_id' => 9, 'file_name' => 'mine_plan.pdf'],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->withSession(['lease_draft' => $mockDraft])
            ->get(route('step4'));

        $response->assertStatus(200);
        // Folder 1: 2/9 files (22%)
        $response->assertSee('<strong>2</strong> / 9', false);
        $response->assertSee('2/9 Saved');
        
        // Folder 2: 1/7 files (14%)
        $response->assertSee('<strong>1</strong> / 7', false);
        $response->assertSee('1/7 Saved');

        // Folder 3: 1/3 files (33%)
        $response->assertSee('<strong>1</strong> / 3', false);
        $response->assertSee('1/3 Saved');

        // Total: 4 of 19 uploaded (21%)
        $response->assertSee('<strong>4</strong> of <strong>19</strong> total documents uploaded', false);
        $response->assertSee('21% Complete');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\DgpsSurvey;
use App\Models\District;
use App\Models\DroneSurvey;
use App\Models\EcCompliance;
use App\Models\EnvironmentProject;
use App\Models\LeaseApplication;
use App\Models\Mineral;
use App\Models\MineralStockpile;
use App\Models\MiningApplication;
use App\Models\PptApplication;
use App\Models\Scopes\BranchScope;
use App\Models\Traits\BelongsToBranch;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class MultiTenancyBranchScopeTest extends TestCase
{
    use DatabaseTransactions;

    protected Branch $branch1;
    protected Branch $branch2;
    protected Customer $customer;
    protected District $district;
    protected Mineral $mineral;
    protected int $categoryId;

    protected User $staffUserBranch1;
    protected User $adminUserRoleId1;
    protected User $adminUserSpatie;
    protected User $superAdminUserSpatie;
    protected User $unassignedUser;

    protected LeaseApplication $leaseBranch1;
    protected LeaseApplication $leaseBranch2;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Branches
        $this->branch1 = Branch::create([
            'branch_name' => 'Branch Alpha ' . uniqid(),
            'contact_person' => 'Alpha Contact',
            'mobile' => '9840111111',
            'address' => 'Alpha Office Road',
            'status' => 1,
        ]);

        $this->branch2 = Branch::create([
            'branch_name' => 'Branch Beta ' . uniqid(),
            'contact_person' => 'Beta Contact',
            'mobile' => '9840222222',
            'address' => 'Beta Office Road',
            'status' => 1,
        ]);

        // 2. Shared reference models
        $this->customer = Customer::first() ?: Customer::create([
            'customer_name' => 'Alpha Minerals Ltd',
            'company_name' => 'Alpha Minerals Ltd',
            'mimas_no' => 'TN/MMS/ALF/' . rand(100, 999),
            'mobile_num' => '9840000000',
            'pan' => 'ALFPA' . rand(1000, 9999) . 'Z',
            'aadhaar_no' => '9840-' . rand(1000, 9999) . '-' . rand(1000, 9999),
            'slug' => 'alpha-minerals-' . rand(1000, 9999),
            'status' => 'active',
        ]);

        $this->district = District::first() ?: District::create([
            'name' => 'Salem',
            'code' => 'SLM',
            'state' => 'Tamil Nadu',
            'status' => 1,
        ]);

        $this->mineral = Mineral::first() ?: Mineral::create([
            'name' => 'Granite',
            'status' => 1,
        ]);

        $catId = DB::table('lease_categories')->value('id');
        if (!$catId) {
            $catId = DB::table('lease_categories')->insertGetId([
                'category_name' => 'Quarry Lease',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->categoryId = $catId;

        // 3. Spatie Roles
        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);

        // 4. Test Users
        // Non-admin assigned to Branch 1
        $this->staffUserBranch1 = User::factory()->create([
            'name' => 'Staff Officer Branch 1',
            'email' => 'staff_b1_' . uniqid() . '@example.com',
            'role_id' => 2,
            'branch_id' => $this->branch1->id,
            'status' => 1,
        ]);
        $this->staffUserBranch1->assignRole('Staff');

        // Admin via role_id === 1 (assigned to Branch 1)
        $this->adminUserRoleId1 = User::factory()->create([
            'name' => 'Admin User RoleId1',
            'email' => 'admin_role1_' . uniqid() . '@example.com',
            'role_id' => 1,
            'branch_id' => $this->branch1->id,
            'status' => 1,
        ]);

        // Admin via Spatie role 'Admin' (role_id !== 1, assigned to Branch 1)
        $this->adminUserSpatie = User::factory()->create([
            'name' => 'Admin User Spatie',
            'email' => 'admin_spatie_' . uniqid() . '@example.com',
            'role_id' => 99,
            'branch_id' => $this->branch1->id,
            'status' => 1,
        ]);
        $this->adminUserSpatie->assignRole('Admin');

        // Super Admin via Spatie role 'Super Admin' (role_id !== 1, assigned to Branch 1)
        $this->superAdminUserSpatie = User::factory()->create([
            'name' => 'Super Admin Spatie',
            'email' => 'superadmin_spatie_' . uniqid() . '@example.com',
            'role_id' => 99,
            'branch_id' => $this->branch1->id,
            'status' => 1,
        ]);
        $this->superAdminUserSpatie->assignRole('Super Admin');

        // User with branch_id = null (unassigned statewide staff)
        $this->unassignedUser = User::factory()->create([
            'name' => 'Unassigned Staff User',
            'email' => 'unassigned_' . uniqid() . '@example.com',
            'role_id' => 2,
            'branch_id' => null,
            'status' => 1,
        ]);
        $this->unassignedUser->assignRole('Staff');

        // 5. Pre-create test records: Record A in Branch 1, Record B in Branch 2
        Auth::logout();

        $this->leaseBranch1 = LeaseApplication::create([
            'application_no' => 'LA-B1-' . uniqid(),
            'customer_id' => $this->customer->id,
            'district_id' => $this->district->id,
            'category_id' => $this->categoryId,
            'mineral_id' => $this->mineral->id,
            'taluk' => 'Taluk 1',
            'village' => 'Village 1',
            'status' => 'draft',
            'branch_id' => $this->branch1->id,
        ]);

        $this->leaseBranch2 = LeaseApplication::create([
            'application_no' => 'LA-B2-' . uniqid(),
            'customer_id' => $this->customer->id,
            'district_id' => $this->district->id,
            'category_id' => $this->categoryId,
            'mineral_id' => $this->mineral->id,
            'taluk' => 'Taluk 2',
            'village' => 'Village 2',
            'status' => 'draft',
            'branch_id' => $this->branch2->id,
        ]);
    }

    /**
     * Group A: Non-Admin User Scope Isolation
     */

    public function test_non_admin_user_restricted_to_own_branch_records(): void
    {
        $this->actingAs($this->staffUserBranch1);

        $results = LeaseApplication::all();

        $this->assertTrue(
            $results->pluck('id')->contains($this->leaseBranch1->id),
            'Staff user must see records belonging to their assigned Branch 1'
        );

        $this->assertFalse(
            $results->pluck('id')->contains($this->leaseBranch2->id),
            'Staff user must NOT see records belonging to Branch 2'
        );

        foreach ($results as $item) {
            $this->assertEquals(
                $this->branch1->id,
                $item->branch_id,
                'Every retrieved record must match authenticated user branch_id'
            );
        }
    }

    public function test_non_admin_cannot_query_or_find_other_branch_record(): void
    {
        $this->actingAs($this->staffUserBranch1);

        // find() must return null for another branch record
        $found = LeaseApplication::find($this->leaseBranch2->id);
        $this->assertNull($found, 'find() for other branch record must return null under BranchScope');

        // where(...)->exists() must return false
        $exists = LeaseApplication::where('id', $this->leaseBranch2->id)->exists();
        $this->assertFalse($exists, 'where()->exists() for other branch record must return false under BranchScope');

        // where(...)->first() must return null
        $first = LeaseApplication::where('id', $this->leaseBranch2->id)->first();
        $this->assertNull($first, 'where()->first() for other branch record must return null under BranchScope');
    }

    /**
     * Group B: Super Admin & Admin Statewide Unrestricted Access Bypass
     */

    public function test_super_admin_bypasses_branch_scope_via_role_id_one(): void
    {
        $this->actingAs($this->adminUserRoleId1);

        $results = LeaseApplication::all();

        $this->assertTrue(
            $results->pluck('id')->contains($this->leaseBranch1->id),
            'Admin with role_id=1 must see Branch 1 records'
        );
        $this->assertTrue(
            $results->pluck('id')->contains($this->leaseBranch2->id),
            'Admin with role_id=1 must see Branch 2 records (statewide bypass)'
        );
    }

    public function test_admin_bypasses_branch_scope_via_spatie_admin_role(): void
    {
        $this->actingAs($this->adminUserSpatie);

        $results = LeaseApplication::all();

        $this->assertTrue(
            $results->pluck('id')->contains($this->leaseBranch1->id),
            'User with Spatie role Admin must see Branch 1 records'
        );
        $this->assertTrue(
            $results->pluck('id')->contains($this->leaseBranch2->id),
            'User with Spatie role Admin must see Branch 2 records (statewide bypass)'
        );
    }

    public function test_super_admin_spatie_role_bypasses_branch_scope(): void
    {
        $this->actingAs($this->superAdminUserSpatie);

        $results = LeaseApplication::all();

        $this->assertTrue(
            $results->pluck('id')->contains($this->leaseBranch1->id),
            'User with Spatie role Super Admin must see Branch 1 records'
        );
        $this->assertTrue(
            $results->pluck('id')->contains($this->leaseBranch2->id),
            'User with Spatie role Super Admin must see Branch 2 records (statewide bypass)'
        );
    }

    /**
     * Group C: Edge Cases (Unassigned Users & Unauthenticated Context)
     */

    public function test_user_with_null_branch_id_is_unscoped(): void
    {
        $this->actingAs($this->unassignedUser);

        $results = LeaseApplication::all();

        $this->assertTrue(
            $results->pluck('id')->contains($this->leaseBranch1->id),
            'User with null branch_id must see Branch 1 records'
        );
        $this->assertTrue(
            $results->pluck('id')->contains($this->leaseBranch2->id),
            'User with null branch_id must see Branch 2 records'
        );
    }

    public function test_unauthenticated_context_is_unscoped(): void
    {
        Auth::logout();

        $results = LeaseApplication::all();

        $this->assertTrue(
            $results->pluck('id')->contains($this->leaseBranch1->id),
            'Unauthenticated context must see Branch 1 records'
        );
        $this->assertTrue(
            $results->pluck('id')->contains($this->leaseBranch2->id),
            'Unauthenticated context must see Branch 2 records'
        );
    }

    /**
     * Group D: Automatic Branch Assignment (BelongsToBranch::creating hook)
     */

    public function test_model_creation_auto_assigns_authenticated_non_admin_user_branch_id_when_empty(): void
    {
        $this->actingAs($this->staffUserBranch1);

        $newApp = LeaseApplication::create([
            'application_no' => 'LA-AUTO-' . uniqid(),
            'customer_id' => $this->customer->id,
            'district_id' => $this->district->id,
            'category_id' => $this->categoryId,
            'mineral_id' => $this->mineral->id,
            'taluk' => 'Auto Taluk',
            'village' => 'Auto Village',
            'status' => 'draft',
            // branch_id is intentionally omitted
        ]);

        $this->assertEquals(
            $this->branch1->id,
            $newApp->branch_id,
            'BelongsToBranch trait must auto-assign authenticated user branch_id when omitted'
        );
    }

    public function test_explicit_branch_id_is_preserved_on_model_creation(): void
    {
        $this->actingAs($this->staffUserBranch1);

        $newApp = LeaseApplication::create([
            'application_no' => 'LA-EXPLICIT-' . uniqid(),
            'customer_id' => $this->customer->id,
            'district_id' => $this->district->id,
            'category_id' => $this->categoryId,
            'mineral_id' => $this->mineral->id,
            'taluk' => 'Explicit Taluk',
            'village' => 'Explicit Village',
            'status' => 'draft',
            'branch_id' => $this->branch2->id, // Explicitly assigned to Branch 2
        ]);

        $this->assertEquals(
            $this->branch2->id,
            $newApp->branch_id,
            'Explicitly provided branch_id must be preserved on model creation'
        );
    }

    /**
     * Group E: Direct Global Scope Bypass
     */

    public function test_without_global_scope_bypasses_branch_scope(): void
    {
        $this->actingAs($this->staffUserBranch1);

        $unscopedResults = LeaseApplication::withoutGlobalScope(BranchScope::class)->get();

        $this->assertTrue(
            $unscopedResults->pluck('id')->contains($this->leaseBranch1->id),
            'withoutGlobalScope must return Branch 1 records'
        );
        $this->assertTrue(
            $unscopedResults->pluck('id')->contains($this->leaseBranch2->id),
            'withoutGlobalScope must return Branch 2 records even for restricted user'
        );
    }

    /**
     * Group F: All 8 Models Registration & Trait Compliance
     */

    public function test_all_eight_models_implement_belongs_to_branch_and_register_branch_scope(): void
    {
        $models = [
            LeaseApplication::class,
            MiningApplication::class,
            EnvironmentProject::class,
            PptApplication::class,
            DgpsSurvey::class,
            DroneSurvey::class,
            EcCompliance::class,
            MineralStockpile::class,
        ];

        foreach ($models as $modelClass) {
            // 1. Verify trait inclusion
            $traits = class_uses_recursive($modelClass);
            $this->assertArrayHasKey(
                BelongsToBranch::class,
                $traits,
                "Model {$modelClass} must use the BelongsToBranch trait"
            );

            // 2. Verify global scope registration
            $instance = new $modelClass();
            $this->assertTrue(
                $instance->hasGlobalScope(BranchScope::class),
                "Model {$modelClass} must register BranchScope global scope"
            );

            // 3. Verify branch relationship
            $relation = $instance->branch();
            $this->assertInstanceOf(
                BelongsTo::class,
                $relation,
                "Model {$modelClass} branch() must return a BelongsTo relationship"
            );
            $this->assertInstanceOf(
                Branch::class,
                $relation->getRelated(),
                "Model {$modelClass} branch() relationship must relate to Branch model"
            );
        }
    }

    /**
     * Group G: Cross-Model Isolation Verification (MineralStockpile)
     */

    public function test_cross_model_branch_isolation_on_mineral_stockpile(): void
    {
        Auth::logout();

        // Create a unique lease application for each stockpile
        $leaseForStockpile1 = LeaseApplication::create([
            'application_no' => 'LA-STK1-' . uniqid(),
            'customer_id' => $this->customer->id,
            'district_id' => $this->district->id,
            'category_id' => $this->categoryId,
            'mineral_id' => $this->mineral->id,
            'branch_id' => $this->branch1->id,
        ]);

        $leaseForStockpile2 = LeaseApplication::create([
            'application_no' => 'LA-STK2-' . uniqid(),
            'customer_id' => $this->customer->id,
            'district_id' => $this->district->id,
            'category_id' => $this->categoryId,
            'mineral_id' => $this->mineral->id,
            'branch_id' => $this->branch2->id,
        ]);

        $stockpile1 = MineralStockpile::create([
            'quarry_customer_id' => $this->customer->id,
            'lease_application_id' => $leaseForStockpile1->id,
            'mineral_id' => $this->mineral->id,
            'branch_id' => $this->branch1->id,
            'annual_permitted_quota' => 5000.00,
            'current_stock_cbm' => 1200.00,
            'total_dispatched_cbm' => 3800.00,
        ]);

        $stockpile2 = MineralStockpile::create([
            'quarry_customer_id' => $this->customer->id,
            'lease_application_id' => $leaseForStockpile2->id,
            'mineral_id' => $this->mineral->id,
            'branch_id' => $this->branch2->id,
            'annual_permitted_quota' => 8000.00,
            'current_stock_cbm' => 2500.00,
            'total_dispatched_cbm' => 5500.00,
        ]);

        // As staff user assigned to Branch 1
        $this->actingAs($this->staffUserBranch1);
        $stockpilesSeen = MineralStockpile::all();

        $this->assertTrue(
            $stockpilesSeen->pluck('id')->contains($stockpile1->id),
            'Staff user must see MineralStockpile belonging to Branch 1'
        );
        $this->assertFalse(
            $stockpilesSeen->pluck('id')->contains($stockpile2->id),
            'Staff user must NOT see MineralStockpile belonging to Branch 2'
        );

        // As admin user
        $this->actingAs($this->adminUserRoleId1);
        $allStockpiles = MineralStockpile::all();

        $this->assertTrue($allStockpiles->pluck('id')->contains($stockpile1->id));
        $this->assertTrue($allStockpiles->pluck('id')->contains($stockpile2->id));
    }
}

<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\District;
use App\Models\LeaseApplication;
use App\Models\Mineral;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class BranchManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $unauthorizedUser;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Ensure required permissions exist
        $permissions = ['branch.view', 'branch.create', 'branch.edit', 'branch.delete', 'users.view'];
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Set up Admin role & user with bypass/all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        $this->adminUser = User::where('role_id', 1)->first() ?: User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin_test_' . uniqid() . '@example.com',
            'role_id' => 1,
            'status' => 1,
        ]);
        if (!$this->adminUser->hasRole('Admin')) {
            $this->adminUser->assignRole('Admin');
        }

        // Set up Staff role & unauthorized user without branch permissions
        $staffRole = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $staffRole->syncPermissions(['dashboard.view', 'customer.view']);

        $this->unauthorizedUser = User::factory()->create([
            'name' => 'Staff Non-Admin User',
            'email' => 'staff_test_' . uniqid() . '@example.com',
            'role_id' => 2,
            'status' => 1,
        ]);
        $this->unauthorizedUser->assignRole('Staff');
    }

    /**
     * Group A: Authentication & Permission Gating
     */

    public function test_guest_redirected_from_all_branch_endpoints(): void
    {
        $this->get(route('branch.index'))->assertRedirect(route('login'));
        $this->post(route('branchadd'), [])->assertRedirect(route('login'));
        $this->post(route('branchedit'), [])->assertRedirect(route('login'));
        $this->post(route('branchdelete'), [])->assertRedirect(route('login'));
    }

    public function test_unauthorized_user_forbidden_on_branch_view(): void
    {
        $response = $this->actingAs($this->unauthorizedUser)->get(route('branch.index'));
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_forbidden_on_branch_create(): void
    {
        $response = $this->actingAs($this->unauthorizedUser)->post(route('branchadd'), [
            'branch_name' => 'Restricted Branch',
            'contact_person' => 'Restricted Person',
            'mobile' => '9876543210',
            'address' => 'Restricted Addr',
        ]);
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_forbidden_on_branch_edit(): void
    {
        $branch = Branch::create([
            'branch_name' => 'Test Branch ' . uniqid(),
            'contact_person' => 'Person',
            'mobile' => '9876543210',
            'address' => 'Address',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->unauthorizedUser)->post(route('branchedit'), [
            'id' => $branch->id,
            'branch_name' => 'Attempted Edit',
            'contact_person' => 'Person',
            'mobile' => '9876543210',
            'address' => 'Address',
        ]);
        $response->assertStatus(403);
    }

    public function test_unauthorized_user_forbidden_on_branch_delete(): void
    {
        $branch = Branch::create([
            'branch_name' => 'Test Branch ' . uniqid(),
            'contact_person' => 'Person',
            'mobile' => '9876543210',
            'address' => 'Address',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->unauthorizedUser)->post(route('branchdelete'), [
            'id' => $branch->id,
        ]);
        $response->assertStatus(403);
    }

    /**
     * Group B: Branch Directory Viewing & Active/Inactive State Rendering
     */

    public function test_authorized_user_can_view_branch_index(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('branch.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.authentication.branch.index');
        $response->assertViewHas('branches');
        $response->assertSee('Departments');
        $response->assertSee('id="example10"', false);
    }

    public function test_branch_index_renders_both_active_and_inactive_branches(): void
    {
        $activeName = 'Active Dept ' . uniqid();
        $inactiveName = 'Inactive Dept ' . uniqid();

        $activeBranch = Branch::create([
            'branch_name' => $activeName,
            'contact_person' => 'Active Manager',
            'mobile' => '9876543210',
            'address' => 'Active Address',
            'status' => 1,
        ]);

        $inactiveBranch = Branch::create([
            'branch_name' => $inactiveName,
            'contact_person' => 'Inactive Manager',
            'mobile' => '9876543211',
            'address' => 'Inactive Address',
            'status' => 0,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('branch.index'));

        $response->assertStatus(200);
        $response->assertSee($activeBranch->branch_name);
        $response->assertSee($inactiveBranch->branch_name);
        $response->assertSee('<span class="badge badge-success">Active</span>', false);
        $response->assertSee('<span class="badge badge-danger">Inactive</span>', false);
    }

    public function test_user_management_filters_inactive_branches_for_department_assignment(): void
    {
        $activeName = 'Active Dept ' . uniqid();
        $inactiveName = 'Inactive Dept ' . uniqid();

        $activeBranch = Branch::create([
            'branch_name' => $activeName,
            'contact_person' => 'Active Officer',
            'mobile' => '9876543220',
            'address' => 'Active Dept Office',
            'status' => 1,
        ]);

        $inactiveBranch = Branch::create([
            'branch_name' => $inactiveName,
            'contact_person' => 'Inactive Officer',
            'mobile' => '9876543221',
            'address' => 'Inactive Dept Office',
            'status' => 0,
        ]);

        $response = $this->actingAs($this->adminUser)->get(route('user.index'));

        $response->assertStatus(200);
        $response->assertViewHas('branch');

        $branchesInDropdown = $response->viewData('branch');
        $this->assertTrue($branchesInDropdown->pluck('id')->contains($activeBranch->id), 'Active branch must appear in user dropdown');
        $this->assertFalse($branchesInDropdown->pluck('id')->contains($inactiveBranch->id), 'Inactive branch must be filtered out from user dropdown');

        foreach ($branchesInDropdown as $item) {
            $this->assertEquals(1, $item->status, 'All branches in user assignment dropdown must be active (status 1)');
        }
    }

    /**
     * Group C: Branch Creation (Validation & Persistence)
     */

    public function test_branch_creation_validation_errors(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson(route('branchadd'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['branch_name', 'contact_person', 'mobile', 'address']);
    }

    public function test_branch_creation_successful_persistence(): void
    {
        $payload = [
            'branch_name' => 'Coimbatore South Dept ' . uniqid(),
            'contact_person' => 'M. Srinivasan',
            'mobile' => '9842109876',
            'address' => 'Collectorate Complex, State Bank Road',
            'city' => 'Coimbatore',
            'state' => 'Tamil Nadu',
            'pincode' => '641018',
        ];

        $response = $this->actingAs($this->adminUser)->postJson(route('branchadd'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'message' => 'Branch Added Successfully',
            'data' => [
                'branch_name' => $payload['branch_name'],
                'contact_person' => $payload['contact_person'],
                'mobile' => $payload['mobile'],
                'address' => $payload['address'],
                'city' => $payload['city'],
                'state' => $payload['state'],
                'pincode' => $payload['pincode'],
                'status' => 1,
            ],
        ]);

        $this->assertDatabaseHas('branches', [
            'branch_name' => $payload['branch_name'],
            'contact_person' => $payload['contact_person'],
            'status' => 1,
            'country' => 'India',
        ]);
    }

    /**
     * Group D: Branch Updating & Status Toggling
     */

    public function test_branch_update_validation_errors(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson(route('branchedit'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['id', 'branch_name', 'contact_person', 'mobile', 'address']);
    }

    public function test_branch_update_metadata_successfully(): void
    {
        $branch = Branch::create([
            'branch_name' => 'Initial Branch ' . uniqid(),
            'contact_person' => 'Initial Person',
            'mobile' => '9000000000',
            'address' => 'Initial Address',
            'city' => 'Madurai',
            'state' => 'Tamil Nadu',
            'pincode' => '625001',
            'status' => 1,
        ]);

        $updatePayload = [
            'id' => $branch->id,
            'branch_name' => 'Updated Branch ' . uniqid(),
            'contact_person' => 'Updated Person',
            'mobile' => '9111111111',
            'address' => 'Updated Address Suite 4B',
            'city' => 'Tiruchirappalli',
            'state' => 'Tamil Nadu',
            'pincode' => '620001',
            'status' => 1,
        ];

        $response = $this->actingAs($this->adminUser)->postJson(route('branchedit'), $updatePayload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'message' => 'Branch Updated Successfully',
        ]);

        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'branch_name' => $updatePayload['branch_name'],
            'contact_person' => $updatePayload['contact_person'],
            'mobile' => $updatePayload['mobile'],
            'address' => $updatePayload['address'],
            'city' => $updatePayload['city'],
            'pincode' => $updatePayload['pincode'],
        ]);
    }

    public function test_toggle_branch_status_to_inactive_and_active(): void
    {
        $branch = Branch::create([
            'branch_name' => 'Toggle Branch ' . uniqid(),
            'contact_person' => 'Status Person',
            'mobile' => '9888877777',
            'address' => 'Status Test Address',
            'status' => 1,
        ]);

        // Toggle to inactive (status = 0)
        $toggleInactivePayload = [
            'id' => $branch->id,
            'branch_name' => $branch->branch_name,
            'contact_person' => $branch->contact_person,
            'mobile' => $branch->mobile,
            'address' => $branch->address,
            'status' => 0,
        ];

        $resInactive = $this->actingAs($this->adminUser)->postJson(route('branchedit'), $toggleInactivePayload);
        $resInactive->assertStatus(200);
        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'status' => 0,
        ]);

        // Toggle back to active (status = 1)
        $toggleActivePayload = array_merge($toggleInactivePayload, ['status' => 1]);
        $resActive = $this->actingAs($this->adminUser)->postJson(route('branchedit'), $toggleActivePayload);
        $resActive->assertStatus(200);
        $this->assertDatabaseHas('branches', [
            'id' => $branch->id,
            'status' => 1,
        ]);
    }

    /**
     * Group E: Branch Deletion & Database Integrity
     */

    public function test_delete_branch_successfully(): void
    {
        $branch = Branch::create([
            'branch_name' => 'Branch To Delete ' . uniqid(),
            'contact_person' => 'Delete Person',
            'mobile' => '9999900000',
            'address' => 'Temporary Delete Office',
            'status' => 1,
        ]);

        $response = $this->actingAs($this->adminUser)->postJson(route('branchdelete'), [
            'id' => $branch->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'message' => 'Branch Deleted Successfully',
        ]);

        $this->assertDatabaseMissing('branches', [
            'id' => $branch->id,
        ]);
    }

    public function test_delete_branch_nullifies_child_application_branch_id(): void
    {
        $branch = Branch::create([
            'branch_name' => 'FK Rel Branch ' . uniqid(),
            'contact_person' => 'FK Contact',
            'mobile' => '9888812345',
            'address' => 'FK Office',
            'status' => 1,
        ]);

        $customer = Customer::first() ?: Customer::create([
            'customer_name' => 'Kaveri Granites Pvt Ltd',
            'company_name' => 'Kaveri Granites Pvt Ltd',
            'mimas_no' => 'TN/MMS/SLM/' . rand(100, 999),
            'mobile_num' => '9842109876',
            'pan' => 'ABCDE' . rand(1000, 9999) . 'F',
            'aadhaar_no' => '9842-' . rand(1000, 9999) . '-' . rand(1000, 9999),
            'slug' => 'kaveri-granites-' . rand(1000, 9999),
            'status' => 1,
        ]);

        $district = District::first() ?: District::create([
            'name' => 'Salem',
            'code' => 'SLM',
            'state' => 'Tamil Nadu',
            'status' => 1,
        ]);

        $mineral = Mineral::first() ?: Mineral::create([
            'name' => 'Black Granite',
            'status' => 1,
        ]);

        $categoryId = DB::table('lease_categories')->value('id');
        if (!$categoryId) {
            $categoryId = DB::table('lease_categories')->insertGetId([
                'category_name' => 'Mining Lease',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $appNo = 'LA-TEST-' . uniqid();
        $lease = LeaseApplication::create([
            'application_no' => $appNo,
            'customer_id' => $customer->id,
            'district_id' => $district->id,
            'category_id' => $categoryId,
            'mineral_id' => $mineral->id,
            'taluk' => 'Omalur',
            'village' => 'Karuppur',
            'status' => 'draft',
            'branch_id' => $branch->id,
        ]);

        $this->assertEquals($branch->id, $lease->branch_id);

        // Delete the branch
        $response = $this->actingAs($this->adminUser)->postJson(route('branchdelete'), [
            'id' => $branch->id,
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseMissing('branches', ['id' => $branch->id]);

        // Verify child record branch_id was nullified by nullOnDelete constraint
        $updatedBranchId = DB::table('lease_applications')->where('id', $lease->id)->value('branch_id');
        $this->assertNull($updatedBranchId, 'LeaseApplication branch_id must be nullified when parent branch is deleted');
    }

    /**
     * Group F: Zero Unhandled 500 Exceptions (Hardened Controller Verification)
     */

    public function test_zero_unhandled_500_on_update_with_missing_or_invalid_id(): void
    {
        // 1. Missing ID returns 422 JSON validation error instead of 500
        $resMissing = $this->actingAs($this->adminUser)->postJson(route('branchedit'), [
            'branch_name' => 'Valid Branch Name',
            'contact_person' => 'Valid Person',
            'mobile' => '9876543210',
            'address' => 'Valid Address',
        ]);
        $resMissing->assertStatus(422);
        $resMissing->assertJsonValidationErrors(['id']);

        // 2. Non-existent ID returns 422 JSON validation error instead of 500
        $resInvalid = $this->actingAs($this->adminUser)->postJson(route('branchedit'), [
            'id' => 99999999,
            'branch_name' => 'Valid Branch Name',
            'contact_person' => 'Valid Person',
            'mobile' => '9876543210',
            'address' => 'Valid Address',
        ]);
        $resInvalid->assertStatus(422);
        $resInvalid->assertJsonValidationErrors(['id']);
    }

    public function test_zero_unhandled_500_on_destroy_with_missing_or_invalid_id(): void
    {
        // 1. Missing ID returns 422 JSON validation error instead of 500
        $resMissing = $this->actingAs($this->adminUser)->postJson(route('branchdelete'), []);
        $resMissing->assertStatus(422);
        $resMissing->assertJsonValidationErrors(['id']);

        // 2. Non-existent ID returns 422 JSON validation error instead of 500
        $resInvalid = $this->actingAs($this->adminUser)->postJson(route('branchdelete'), [
            'id' => 99999999,
        ]);
        $resInvalid->assertStatus(422);
        $resInvalid->assertJsonValidationErrors(['id']);
    }

    /**
     * Group E: Branch Deletion Safeguards & User Cascade (Grill-Me Decided)
     */

    public function test_last_branch_cannot_be_deleted(): void
    {
        $primaryBranch = Branch::first();
        $this->assertNotNull($primaryBranch);

        // Keep only 1 branch in database
        $branches = Branch::where('id', '!=', $primaryBranch->id)->get();
        foreach ($branches as $b) {
            User::where('branch_id', $b->id)->delete();
            $b->delete();
        }

        $this->assertEquals(1, Branch::count());

        $response = $this->actingAs($this->adminUser)->postJson(route('branchdelete'), [
            'id' => $primaryBranch->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 0,
            'message' => 'The last remaining Department / Branch cannot be deleted.',
        ]);

        $this->assertDatabaseHas('branches', ['id' => $primaryBranch->id]);
    }

    public function test_cannot_delete_own_branch(): void
    {
        $ownBranch = Branch::find($this->adminUser->branch_id) ?: Branch::first();
        $this->adminUser->branch_id = $ownBranch->id;
        $this->adminUser->save();

        // Create an auxiliary second branch so total count > 1
        $otherBranch = Branch::create([
            'branch_name' => 'Auxiliary Branch ' . uniqid(),
            'contact_person' => 'Aux Contact',
            'mobile' => '9888800000',
            'address' => 'Aux Address',
            'status' => 1,
        ]);

        // Attempt to delete the branch that the current adminUser belongs to
        $response = $this->actingAs($this->adminUser)->postJson(route('branchdelete'), [
            'id' => $ownBranch->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 0,
            'message' => 'You cannot delete the department your own account is assigned to.',
        ]);

        $this->assertDatabaseHas('branches', ['id' => $ownBranch->id]);
    }

    public function test_cannot_delete_branch_holding_only_remaining_admin(): void
    {
        // Create an auxiliary branch
        $branchWithAdmin = Branch::create([
            'branch_name' => 'Admin Only Branch ' . uniqid(),
            'contact_person' => 'Sole Admin Host',
            'mobile' => '9777700000',
            'address' => 'Admin Only Address',
            'status' => 1,
        ]);

        // Move admin to this branch and make sure it's the only admin
        $this->adminUser->branch_id = $branchWithAdmin->id;
        $this->adminUser->save();

        // Ensure other users with admin role in other branches don't exist
        $otherAdmins = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))
            ->where('id', '!=', $this->adminUser->id)
            ->get();
        foreach ($otherAdmins as $oa) {
            $oa->removeRole('Admin');
        }

        // Create an officer with branch.delete permission in another branch to perform the deletion
        $anotherBranch = Branch::create([
            'branch_name' => 'Actor Branch ' . uniqid(),
            'contact_person' => 'Actor Contact',
            'mobile' => '9666600000',
            'address' => 'Actor Address',
            'status' => 1,
        ]);

        $officerActor = User::create([
            'name' => 'Officer Actor',
            'email' => 'officer_actor_' . uniqid() . '@example.com',
            'password' => bcrypt('Pass@123'),
            'role_id' => 3,
            'branch_id' => $anotherBranch->id,
            'status' => 1,
        ]);
        $officerActor->givePermissionTo('branch.delete');

        // Officer attempts to delete branchWithAdmin which contains the sole admin
        $response = $this->actingAs($officerActor)->postJson(route('branchdelete'), [
            'id' => $branchWithAdmin->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 0,
            'message' => 'Cannot delete this department because it contains the only remaining Admin account.',
        ]);

        $this->assertDatabaseHas('branches', ['id' => $branchWithAdmin->id]);
    }

    public function test_branch_deletion_cascades_assigned_users(): void
    {
        // 1. Create a branch to delete
        $targetBranch = Branch::create([
            'branch_name' => 'Cascade Target Branch ' . uniqid(),
            'contact_person' => 'Cascade Person',
            'mobile' => '9555500000',
            'address' => 'Cascade Address',
            'status' => 1,
        ]);

        // 2. Create users assigned to this branch
        $user1 = User::create([
            'name' => 'Sub User 1',
            'email' => 'sub1_' . uniqid() . '@example.com',
            'password' => bcrypt('Pass@123'),
            'role_id' => 2,
            'branch_id' => $targetBranch->id,
            'status' => 1,
        ]);
        $user2 = User::create([
            'name' => 'Sub User 2',
            'email' => 'sub2_' . uniqid() . '@example.com',
            'password' => bcrypt('Pass@123'),
            'role_id' => 2,
            'branch_id' => $targetBranch->id,
            'status' => 1,
        ]);

        $this->assertDatabaseHas('users', ['id' => $user1->id]);
        $this->assertDatabaseHas('users', ['id' => $user2->id]);

        // 3. Admin deletes the target branch
        $response = $this->actingAs($this->adminUser)->postJson(route('branchdelete'), [
            'id' => $targetBranch->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
        ]);

        // 4. Verify branch is gone AND both assigned users are cascade deleted
        $this->assertDatabaseMissing('branches', ['id' => $targetBranch->id]);
        $this->assertDatabaseMissing('users', ['id' => $user1->id]);
        $this->assertDatabaseMissing('users', ['id' => $user2->id]);
    }
}

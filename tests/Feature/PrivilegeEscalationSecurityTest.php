<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\District;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PrivilegeEscalationSecurityTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $managerUser;
    protected Role $adminRole;
    protected Role $managerRole;
    protected Role $restrictedRole;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->branch = Branch::firstOrCreate(
            ['branch_name' => 'HQ Security Branch'],
            [
                'contact_person' => 'Sec Officer',
                'mobile' => '9876543210',
                'city' => 'Chennai',
                'state' => 'Tamil Nadu',
                'pincode' => '600001',
                'status' => 1
            ]
        );

        $this->adminRole = Role::firstOrCreate(['name' => 'Admin'], ['guard_name' => 'web']);
        $this->managerRole = Role::firstOrCreate(['name' => 'Mining Supervisor'], ['guard_name' => 'web']);
        $this->restrictedRole = Role::firstOrCreate(['name' => 'Finance Auditor'], ['guard_name' => 'web']);

        // Give supervisor limited permissions (users and roles manage + mining, but NO accounts)
        $allowedPerms = ['users.view', 'users.create', 'users.edit', 'users.delete', 'roles.view', 'roles.create', 'roles.edit', 'roles.delete', 'mining.view', 'mining.create'];
        foreach ($allowedPerms as $perm) {
            Permission::findOrCreate($perm, 'web');
        }
        $this->managerRole->syncPermissions($allowedPerms);

        // Restricted role has account permissions
        Permission::findOrCreate('account.view', 'web');
        Permission::findOrCreate('account.create', 'web');
        $this->restrictedRole->syncPermissions(['account.view', 'account.create']);

        // Admin user
        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin_sec@gtms.test'],
            [
                'name' => 'Sec Admin',
                'password' => bcrypt('AdminPass123'),
                'role_id' => $this->adminRole->id,
                'branch_id' => $this->branch->id,
                'status' => 1,
            ]
        );
        $this->adminUser->syncRoles([$this->adminRole->name]);

        // Manager user
        $this->managerUser = User::firstOrCreate(
            ['email' => 'supervisor_sec@gtms.test'],
            [
                'name' => 'Sec Supervisor',
                'password' => bcrypt('SupervisorPass123'),
                'role_id' => $this->managerRole->id,
                'branch_id' => $this->branch->id,
                'status' => 1,
            ]
        );
        $this->managerUser->syncRoles([$this->managerRole->name]);
    }

    public function test_non_superadmin_user_directory_excludes_admin_roles_from_dropdown(): void
    {
        $response = $this->actingAs($this->managerUser)->get('/user');
        $response->assertStatus(200);

        $viewRoles = $response->viewData('role');

        // Dropdown must not include Admin or Super Admin
        $this->assertFalse($viewRoles->contains('name', 'Admin'));
        $this->assertFalse($viewRoles->contains('name', 'Super Admin'));

        // Dropdown must not include roles with permissions exceeding the user's scope (Finance Auditor has account perms)
        $this->assertFalse($viewRoles->contains('name', 'Finance Auditor'));
    }

    public function test_superadmin_user_directory_includes_all_roles(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/user');
        $response->assertStatus(200);

        $viewRoles = $response->viewData('role');
        $this->assertTrue($viewRoles->contains('name', 'Admin'));
    }

    public function test_non_superadmin_cannot_assign_admin_role_on_user_creation(): void
    {
        $payload = [
            'name' => 'Escalated User',
            'email' => 'escalate_' . uniqid() . '@gtms.test',
            'password' => 'Secret123',
            'role_id' => $this->adminRole->id,
            'branch_id' => $this->branch->id,
        ];

        $response = $this->actingAs($this->managerUser)->postJson('/useradd', $payload);
        $this->assertContains($response->getStatusCode(), [200, 403]);
        if ($response->getStatusCode() === 200) {
            $this->assertEquals(0, $response->json('status'));
        }
    }

    public function test_non_superadmin_cannot_assign_role_exceeding_their_permissions(): void
    {
        $payload = [
            'name' => 'Finance User',
            'email' => 'finance_' . uniqid() . '@gtms.test',
            'password' => 'Secret123',
            'role_id' => $this->restrictedRole->id,
            'branch_id' => $this->branch->id,
        ];

        $response = $this->actingAs($this->managerUser)->postJson('/useradd', $payload);
        $this->assertContains($response->getStatusCode(), [200, 403]);
        if ($response->getStatusCode() === 200) {
            $this->assertEquals(0, $response->json('status'));
        }
    }

    public function test_user_cannot_modify_their_own_security_role(): void
    {
        $payload = [
            'id' => $this->managerUser->id,
            'name' => $this->managerUser->name,
            'email' => $this->managerUser->email,
            'role_id' => $this->adminRole->id, // Attempt self-promotion to Admin
            'branch_id' => $this->branch->id,
        ];

        $response = $this->actingAs($this->managerUser)->postJson('/useredit', $payload);
        $this->assertContains($response->getStatusCode(), [200, 403]);
        if ($response->getStatusCode() === 200) {
            $this->assertEquals(0, $response->json('status'));
        }

        // Verify role remained unchanged
        $this->managerUser->refresh();
        $this->assertEquals($this->managerRole->id, $this->managerUser->role_id);
    }

    public function test_non_superadmin_cannot_modify_admin_account(): void
    {
        $payload = [
            'id' => $this->adminUser->id,
            'name' => 'Tampered Admin Name',
            'email' => $this->adminUser->email,
            'role_id' => $this->managerRole->id,
            'branch_id' => $this->branch->id,
        ];

        $response = $this->actingAs($this->managerUser)->postJson('/useredit', $payload);
        $this->assertContains($response->getStatusCode(), [200, 403]);
        if ($response->getStatusCode() === 200) {
            $this->assertEquals(0, $response->json('status'));
        }
    }

    public function test_non_superadmin_cannot_grant_unowned_permissions_when_creating_role(): void
    {
        $payload = [
            'name' => 'Exploit Role ' . uniqid(),
            'permissions' => ['account.view', 'account.create'], // Permissions not held by manager
        ];

        $response = $this->actingAs($this->managerUser)->postJson('/roleadd', $payload);
        $this->assertContains($response->getStatusCode(), [200, 403]);
        if ($response->getStatusCode() === 200) {
            $this->assertEquals(0, $response->json('status'));
        }
    }

    public function test_non_superadmin_cannot_create_reserved_admin_role_names(): void
    {
        $payload = [
            'name' => 'Superadmin',
            'permissions' => ['mining.view'],
        ];

        $response = $this->actingAs($this->managerUser)->postJson('/roleadd', $payload);
        $this->assertContains($response->getStatusCode(), [200, 403]);
        if ($response->getStatusCode() === 200) {
            $this->assertEquals(0, $response->json('status'));
        }
    }

    public function test_roles_view_scopes_permissions_to_user_owned_subset(): void
    {
        $response = $this->actingAs($this->managerUser)->get('/roles');
        $response->assertStatus(200);

        $groupedPerms = $response->viewData('groupedPermissions');

        // Accounts module should NOT be present in manager's permissions list
        $this->assertArrayNotHasKey('account', $groupedPerms);

        // Mining module SHOULD be present
        $this->assertArrayHasKey('mining', $groupedPerms);
    }

    public function test_non_superadmin_roles_view_only_displays_roles_created_by_themselves(): void
    {
        // 1. Initially, manager has not created any roles, so roles list is empty for manager
        $response = $this->actingAs($this->managerUser)->get('/roles');
        $response->assertStatus(200);
        $roles = $response->viewData('roles');

        // Must not contain roles created by other users (Admin)
        $this->assertFalse($roles->contains('name', 'Admin'));
        $this->assertFalse($roles->contains('name', 'Super Admin'));
        $this->assertFalse($roles->contains('name', 'Staff'));
        $this->assertFalse($roles->contains('name', 'Officer'));

        // 2. Manager creates a role
        $createRes = $this->actingAs($this->managerUser)->postJson('/roleadd', [
            'name' => 'Supervisor Junior ' . uniqid(),
            'permissions' => ['mining.view'],
        ]);
        $this->assertEquals(200, $createRes->getStatusCode());
        $createdRoleId = $createRes->json('data.id');

        // 3. Manager now sees their own created role
        $response2 = $this->actingAs($this->managerUser)->get('/roles');
        $roles2 = $response2->viewData('roles');
        $this->assertTrue($roles2->contains('id', $createdRoleId));

        // 4. Manager cannot update a role created by Admin
        $updateRes = $this->actingAs($this->managerUser)->postJson('/roleupdate', [
            'id' => $this->adminRole->id,
            'name' => 'Hacked Admin',
            'permissions' => ['mining.view'],
        ]);
        $this->assertEquals(403, $updateRes->getStatusCode());

        // 5. Manager cannot delete a role created by Admin
        $deleteRes = $this->actingAs($this->managerUser)->postJson('/roledelete', [
            'id' => $this->adminRole->id,
        ]);
        $this->assertContains($deleteRes->getStatusCode(), [200, 403]);
    }

    public function test_non_superadmin_user_directory_only_shows_self_and_created_subordinates(): void
    {
        // 1. Manager views user directory: must only contain self, not other admin users
        $response = $this->actingAs($this->managerUser)->get('/user');
        $response->assertStatus(200);

        $users = $response->viewData('users');
        $this->assertTrue($users->contains('id', $this->managerUser->id));
        $this->assertFalse($users->contains('id', $this->adminUser->id));

        // 2. Manager creates their own role first
        $roleRes = $this->actingAs($this->managerUser)->postJson('/roleadd', [
            'name' => 'Subordinate Clerk ' . uniqid(),
            'permissions' => ['mining.view'],
        ]);
        $this->assertEquals(200, $roleRes->getStatusCode());
        $roleId = $roleRes->json('data.id');

        // 3. Manager creates a subordinate user
        $userRes = $this->actingAs($this->managerUser)->postJson('/useradd', [
            'name' => 'Subordinate Worker',
            'email' => 'subordinate_' . uniqid() . '@gtms.test',
            'password' => 'Pass123456',
            'role_id' => $roleId,
            'branch_id' => $this->branch->id,
        ]);
        $this->assertEquals(200, $userRes->getStatusCode());
        $subordinateId = $userRes->json('data.id');

        // 4. Directory now includes self + newly created subordinate
        $response2 = $this->actingAs($this->managerUser)->get('/user');
        $users2 = $response2->viewData('users');
        $this->assertTrue($users2->contains('id', $this->managerUser->id));
        $this->assertTrue($users2->contains('id', $subordinateId));
        $this->assertFalse($users2->contains('id', $this->adminUser->id));

        // 5. Manager cannot delete a user created by Admin
        $delRes = $this->actingAs($this->managerUser)->postJson('/userdelete', [
            'id' => $this->adminUser->id,
        ]);
        $this->assertContains($delRes->getStatusCode(), [200, 403]);
        if ($delRes->getStatusCode() === 200) {
            $this->assertEquals(0, $delRes->json('status'));
        }
    }

    public function test_non_superadmin_cannot_view_or_assign_other_branches(): void
    {
        // 1. Create a separate branch that does not belong to managerUser
        $foreignBranch = Branch::firstOrCreate(
            ['branch_name' => 'Foreign Distant Branch'],
            [
                'contact_person' => 'Other Manager',
                'mobile' => '9998887776',
                'city' => 'Madurai',
                'state' => 'Tamil Nadu',
                'pincode' => '625001',
                'status' => 1
            ]
        );

        // 2. Manager accesses /user: branch dropdown must ONLY contain their own branch
        $response = $this->actingAs($this->managerUser)->get('/user');
        $response->assertStatus(200);

        $branches = $response->viewData('branch');
        $this->assertTrue($branches->contains('id', $this->branch->id));
        $this->assertFalse($branches->contains('id', $foreignBranch->id));

        // 3. Manager creates role
        $roleRes = $this->actingAs($this->managerUser)->postJson('/roleadd', [
            'name' => 'Branch Test Role ' . uniqid(),
            'permissions' => ['mining.view'],
        ]);
        $roleId = $roleRes->json('data.id');

        // 4. Manager attempts to assign foreign branch to a new user: rejected (403)
        $addRes = $this->actingAs($this->managerUser)->postJson('/useradd', [
            'name' => 'Foreign Assigned User',
            'email' => 'foreign_' . uniqid() . '@gtms.test',
            'password' => 'Pass123456',
            'role_id' => $roleId,
            'branch_id' => $foreignBranch->id, // Unauthorized branch
        ]);
        $this->assertEquals(403, $addRes->getStatusCode());
    }

    public function test_non_superadmin_customer_directory_only_shows_own_branch_customers(): void
    {
        Permission::findOrCreate('customer.view', 'web');
        Permission::findOrCreate('customer.create', 'web');
        $this->managerUser->givePermissionTo(['customer.view', 'customer.create']);

        $district = \App\Models\District::firstOrCreate(['name' => 'Salem'], ['status' => 1]);

        $foreignBranch = Branch::firstOrCreate(
            ['branch_name' => 'Foreign Branch C ' . uniqid()],
            [
                'contact_person' => 'Other Branch Lead',
                'mobile' => '9840999888',
                'address' => 'Foreign Office Street',
                'status' => 1
            ]
        );

        // 1. Customer in foreign branch
        $foreignCustomer = Customer::create([
            'customer_name' => 'Foreign Client ' . uniqid(),
            'mimas_no' => 'TN/MMS/FC/' . rand(1000, 9999),
            'mobile_num' => '9840112233',
            'district_id' => $district->id,
            'status' => 1,
            'branch_id' => $foreignBranch->id,
            'created_by' => $this->adminUser->id,
        ]);

        // 2. Customer in manager's own branch
        $ownCustomer = Customer::create([
            'customer_name' => 'Local Client ' . uniqid(),
            'mimas_no' => 'TN/MMS/LC/' . rand(1000, 9999),
            'mobile_num' => '9840445566',
            'district_id' => $district->id,
            'status' => 1,
            'branch_id' => $this->managerUser->branch_id,
            'created_by' => $this->managerUser->id,
        ]);

        // 3. Manager accesses customer directory: foreign customer must be completely invisible
        $response = $this->actingAs($this->managerUser)->get('/customers');
        $response->assertStatus(200);

        $viewCustomers = $response->viewData('customers');
        $this->assertTrue($viewCustomers->contains('id', $ownCustomer->id));
        $this->assertFalse($viewCustomers->contains('id', $foreignCustomer->id));

        // 4. Super Admin accesses customer directory: sees both
        $adminResponse = $this->actingAs($this->adminUser)->get('/customers');
        $adminResponse->assertStatus(200);
        $adminCustomers = $adminResponse->viewData('customers');
        $this->assertTrue($adminCustomers->contains('id', $ownCustomer->id));
        $this->assertTrue($adminCustomers->contains('id', $foreignCustomer->id));
    }

    public function test_customer_in_other_branch_hidden_from_workflow_dropdowns_and_direct_lookup(): void
    {
        Permission::findOrCreate('customer.view', 'web');
        $this->managerUser->givePermissionTo(['customer.view']);

        $district = \App\Models\District::firstOrCreate(['name' => 'Salem'], ['status' => 1]);

        $foreignBranch = Branch::firstOrCreate(
            ['branch_name' => 'Foreign Branch D ' . uniqid()],
            [
                'contact_person' => 'Other Branch Head',
                'mobile' => '9840888777',
                'address' => 'Distant Road',
                'status' => 1
            ]
        );

        $foreignCustomer = Customer::create([
            'customer_name' => 'Distant Customer ' . uniqid(),
            'mimas_no' => 'TN/MMS/DC/' . rand(1000, 9999),
            'mobile_num' => '9840777666',
            'district_id' => $district->id,
            'status' => 1,
            'branch_id' => $foreignBranch->id,
            'created_by' => $this->adminUser->id,
        ]);

        $ownCustomer = Customer::create([
            'customer_name' => 'Local Subordinate Client ' . uniqid(),
            'mimas_no' => 'TN/MMS/LSC/' . rand(1000, 9999),
            'mobile_num' => '9840333222',
            'district_id' => $district->id,
            'status' => 1,
            'branch_id' => $this->managerUser->branch_id,
            'created_by' => $this->managerUser->id,
        ]);

        // When non-superadmin queries Customer model, foreign customer is excluded by BranchScope
        $this->actingAs($this->managerUser);
        $customersForDropdown = Customer::all();
        $this->assertTrue($customersForDropdown->contains('id', $ownCustomer->id));
        $this->assertFalse($customersForDropdown->contains('id', $foreignCustomer->id));

        // Direct show attempt of foreign customer returns 404
        $showRes = $this->get('/customers/' . $foreignCustomer->slug);
        $showRes->assertStatus(404);
    }
}

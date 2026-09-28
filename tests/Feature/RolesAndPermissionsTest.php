<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RolesAndPermissionsTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $staffUser;
    protected User $officerUser;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Setup Admin user
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $admin = User::where('email', 'admin@gtms.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@gtms.com',
                'password' => bcrypt('admin123'),
                'role_id' => $adminRole->id,
                'status' => 1,
                'user_code' => 'LUK_001',
            ]);
        }
        $admin->syncRoles(['Admin']);
        $this->adminUser = $admin;

        // 2. Setup Staff user (lacks roles.* permissions)
        $staffRole = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $staff = User::where('email', 'staff_test_r2@gtms.com')->first();
        if (!$staff) {
            $staff = User::create([
                'name' => 'Staff Test User',
                'email' => 'staff_test_r2@gtms.com',
                'password' => bcrypt('staff123'),
                'role_id' => $staffRole->id,
                'status' => 1,
                'user_code' => 'LUK_997',
            ]);
        }
        $staff->syncRoles(['Staff']);
        $this->staffUser = $staff;

        // 3. Setup Officer user
        $officerRole = Role::firstOrCreate(['name' => 'Officer', 'guard_name' => 'web']);
        $officer = User::where('email', 'officer_test_r2@gtms.com')->first();
        if (!$officer) {
            $officer = User::create([
                'name' => 'Officer Test User',
                'email' => 'officer_test_r2@gtms.com',
                'password' => bcrypt('officer123'),
                'role_id' => $officerRole->id,
                'status' => 1,
                'user_code' => 'LUK_998',
            ]);
        }
        $officer->syncRoles(['Officer']);
        $this->officerUser = $officer;
    }

    /**
     * Requirement R2.1: Guest redirects on all roles endpoints
     */
    public function test_guest_cannot_access_roles_endpoints(): void
    {
        $this->get('/roles')->assertRedirect(route('login'));
        $this->get('roles/1/permissions')->assertRedirect(route('login'));
        $this->post('/roleadd', ['name' => 'Unauth Role'])->assertRedirect(route('login'));
        $this->post('/roleupdate', ['id' => 1, 'name' => 'Unauth Update'])->assertRedirect(route('login'));
        $this->post('/roledelete', ['id' => 1])->assertRedirect(route('login'));
    }

    /**
     * Requirement R2.2: Permission middleware gating (403 for unauthorized users lacking roles.* permissions)
     */
    public function test_unauthorized_user_is_forbidden_from_roles_endpoints(): void
    {
        $this->actingAs($this->staffUser);

        $this->get('/roles')->assertStatus(403);
        $this->get('roles/1/permissions')->assertStatus(403);
        $this->post('/roleadd', ['name' => 'Staff Role'])->assertStatus(403);
        $this->post('/roleupdate', ['id' => 1, 'name' => 'Staff Edit'])->assertStatus(403);
        $this->post('/roledelete', ['id' => 1])->assertStatus(403);
    }

    /**
     * Requirement R2.2: User with granular permission can only perform permitted action
     */
    public function test_granular_permission_gating(): void
    {
        // Create custom user with only roles.view
        $viewOnlyUser = User::create([
            'name' => 'View Only User',
            'email' => 'view_only_' . uniqid() . '@gtms.com',
            'password' => bcrypt('password123'),
            'status' => 1,
        ]);
        $viewOnlyUser->givePermissionTo('roles.view');

        $this->actingAs($viewOnlyUser);

        // roles.view allows GET /roles and GET roles/{id}/permissions
        $testRole = Role::firstOrCreate(['name' => 'Perm Test Role', 'guard_name' => 'web']);
        $this->get('/roles')->assertStatus(200);
        $this->get('roles/' . $testRole->id . '/permissions')->assertStatus(200);

        // but forbids create, edit, delete
        $this->post('/roleadd', ['name' => 'Forbidden Create'])->assertStatus(403);
        $this->post('/roleupdate', ['id' => 1, 'name' => 'Forbidden Update'])->assertStatus(403);
        $this->post('/roledelete', ['id' => 1])->assertStatus(403);
    }

    /**
     * Requirement R2.3: Viewing role matrix and assigned permission counts
     */
    public function test_admin_can_view_role_matrix_and_permission_counts(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/roles');

        $response->assertStatus(200);
        $response->assertViewIs('pages.authentication.roles.index');
        $response->assertViewHasAll(['roles', 'groupedPermissions', 'modules']);
        $response->assertSee('Admin');
        $response->assertSee('All Permissions (Super Admin)');

        $viewRoles = $response->viewData('roles');
        $this->assertNotEmpty($viewRoles);
        $this->assertTrue($viewRoles->contains('name', 'Admin'));

        $modules = $response->viewData('modules');
        $this->assertArrayHasKey('dashboard', $modules);
        $this->assertArrayHasKey('roles', $modules);
        $this->assertArrayHasKey('users', $modules);
    }

    /**
     * Requirement R2.4: Dynamic AJAX fetching of role permissions via roles/{id}/permissions
     */
    public function test_can_fetch_role_permissions_via_ajax(): void
    {
        $testRole = Role::create([
            'name' => 'AJAX Test Role ' . uniqid(),
            'guard_name' => 'web',
        ]);
        $testRole->syncPermissions(['dashboard.view', 'customer.view', 'mining.view']);

        $response = $this->actingAs($this->adminUser)->get('roles/' . $testRole->id . '/permissions');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'role' => ['id', 'name', 'guard_name'],
            'permissions',
        ]);
        $response->assertJson([
            'status' => 1,
            'role' => [
                'id' => $testRole->id,
                'name' => $testRole->name,
            ],
        ]);

        $permissions = $response->json('permissions');
        $this->assertIsArray($permissions);
        $this->assertContains('dashboard.view', $permissions);
        $this->assertContains('customer.view', $permissions);
        $this->assertContains('mining.view', $permissions);
        $this->assertNotContains('roles.delete', $permissions);
    }

    /**
     * Requirement R2.4: AJAX fetching returns 404 for invalid ID
     */
    public function test_get_permissions_returns_404_for_invalid_id(): void
    {
        $this->actingAs($this->adminUser)
            ->get('roles/99999999/permissions')
            ->assertStatus(404);
    }

    /**
     * Requirement R2.5: Creating new roles and synchronizing permissions, cache purging
     */
    public function test_admin_can_create_new_role_with_permissions(): void
    {
        $roleName = 'Quality Auditor ' . uniqid();
        $payload = [
            'name' => $roleName,
            'permissions' => ['customer.view', 'mining.view', 'dgps.view'],
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/roleadd', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'message' => 'Role Added Successfully',
            'data' => [
                'name' => $roleName,
            ],
        ]);

        $this->assertDatabaseHas('roles', ['name' => $roleName]);
        $createdRole = Role::where('name', $roleName)->first();
        $this->assertNotNull($createdRole);
        $this->assertCount(3, $createdRole->permissions);
        $this->assertTrue($createdRole->hasPermissionTo('customer.view'));
        $this->assertTrue($createdRole->hasPermissionTo('mining.view'));
        $this->assertTrue($createdRole->hasPermissionTo('dgps.view'));
    }

    /**
     * Requirement R2.6: Role creation validation (required name, unique name)
     */
    public function test_role_creation_validation_rules(): void
    {
        // 1. Missing name
        $this->actingAs($this->adminUser)
            ->postJson('/roleadd', ['name' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        // 2. Duplicate name
        $this->actingAs($this->adminUser)
            ->postJson('/roleadd', ['name' => 'Admin'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Requirement R2.6: Role creation rejects non-existent permissions safely (no 500)
     */
    public function test_role_creation_validates_permission_names_safely(): void
    {
        $this->actingAs($this->adminUser)
            ->postJson('/roleadd', [
                'name' => 'Malicious Perm Role ' . uniqid(),
                'permissions' => ['completely.invalid.permission.key'],
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['permissions.0']);
    }

    /**
     * Requirement R2.7: Updating role names and updating permissions
     */
    public function test_admin_can_update_role_and_modify_permissions(): void
    {
        $role = Role::create([
            'name' => 'Initial Role ' . uniqid(),
            'guard_name' => 'web',
        ]);
        $role->syncPermissions(['customer.view', 'application.view']);

        $updatedName = 'Renamed Role ' . uniqid();
        $payload = [
            'id' => $role->id,
            'name' => $updatedName,
            'permissions' => ['mining.view', 'drone.view'],
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/roleupdate', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'message' => 'Role Updated Successfully',
        ]);

        $role->refresh();
        $this->assertEquals($updatedName, $role->name);
        $perms = $role->permissions->pluck('name')->toArray();
        $this->assertContains('mining.view', $perms);
        $this->assertContains('drone.view', $perms);
        $this->assertNotContains('customer.view', $perms);
        $this->assertNotContains('application.view', $perms);
    }

    /**
     * Requirement R2.7: Updating role without permissions revokes all permissions
     */
    public function test_update_role_without_permissions_revokes_all(): void
    {
        $role = Role::create([
            'name' => 'Perms Clear Role ' . uniqid(),
            'guard_name' => 'web',
        ]);
        $role->syncPermissions(['customer.view', 'mining.view']);

        // Post without permissions key
        $response = $this->actingAs($this->adminUser)->postJson('/roleupdate', [
            'id' => $role->id,
            'name' => $role->name,
        ]);

        $response->assertStatus(200);
        $this->assertCount(0, $role->fresh()->permissions);

        // Also test passing permissions as empty array
        $role->syncPermissions(['customer.view']);
        $this->assertCount(1, $role->fresh()->permissions);

        $response2 = $this->actingAs($this->adminUser)->postJson('/roleupdate', [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => [],
        ]);

        $response2->assertStatus(200);
        $this->assertCount(0, $role->fresh()->permissions);
    }

    /**
     * Requirement R2.7: Role update validation enforces required fields, existence, and uniqueness
     */
    public function test_role_update_validation_errors(): void
    {
        // 1. Missing id
        $this->actingAs($this->adminUser)
            ->postJson('/roleupdate', ['name' => 'Valid Name'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['id']);

        // 2. Non-existent id
        $this->actingAs($this->adminUser)
            ->postJson('/roleupdate', ['id' => 99999999, 'name' => 'Valid Name'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['id']);

        // 3. Duplicate name with another role
        $roleA = Role::create(['name' => 'UniqueRoleA_' . uniqid(), 'guard_name' => 'web']);
        $roleB = Role::create(['name' => 'UniqueRoleB_' . uniqid(), 'guard_name' => 'web']);

        $this->actingAs($this->adminUser)
            ->postJson('/roleupdate', ['id' => $roleB->id, 'name' => $roleA->name])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Requirement R2.8: Destruction safeguards (Admin and Super Admin roles cannot be deleted)
     */
    public function test_destruction_safeguards_prevent_deleting_admin_and_super_admin(): void
    {
        // 1. Safeguard on Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        $this->assertNotNull($adminRole);

        $response = $this->actingAs($this->adminUser)->postJson('/roledelete', [
            'id' => $adminRole->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 0,
            'message' => 'Default Administrator role cannot be deleted.',
        ]);
        $this->assertDatabaseHas('roles', ['id' => $adminRole->id, 'name' => 'Admin']);

        // 2. Safeguard on Super Admin role
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);

        $saResponse = $this->actingAs($this->adminUser)->postJson('/roledelete', [
            'id' => $superAdminRole->id,
        ]);

        $saResponse->assertStatus(200);
        $saResponse->assertJson([
            'status' => 0,
            'message' => 'Default Administrator role cannot be deleted.',
        ]);
        $this->assertDatabaseHas('roles', ['id' => $superAdminRole->id, 'name' => 'Super Admin']);
    }

    /**
     * Requirement R2.9: Custom role deletion succeeds and cleans up database
     */
    public function test_custom_role_deletion_succeeds(): void
    {
        $role = Role::create([
            'name' => 'Disposable Role ' . uniqid(),
            'guard_name' => 'web',
        ]);
        $role->syncPermissions(['customer.view']);

        $this->assertDatabaseHas('roles', ['id' => $role->id]);

        $response = $this->actingAs($this->adminUser)->postJson('/roledelete', [
            'id' => $role->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'message' => 'Role Deleted Successfully',
        ]);

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    /**
     * Requirement R2.10: Zero unhandled 500 exceptions across all role endpoints
     */
    public function test_zero_unhandled_500_exceptions_on_edge_case_requests(): void
    {
        $this->actingAs($this->adminUser);

        // Non-existent role permissions: should 404, never 500
        $res1 = $this->get('roles/99999999/permissions');
        $this->assertNotEquals(500, $res1->getStatusCode());
        $this->assertEquals(404, $res1->getStatusCode());

        // Malformed roleadd payload: should 422, never 500
        $res2 = $this->postJson('/roleadd', ['invalid_key' => 'garbage']);
        $this->assertNotEquals(500, $res2->getStatusCode());
        $this->assertEquals(422, $res2->getStatusCode());

        // Malformed roleupdate payload: should 422, never 500
        $res3 = $this->postJson('/roleupdate', ['id' => 'not-an-id']);
        $this->assertNotEquals(500, $res3->getStatusCode());
        $this->assertEquals(422, $res3->getStatusCode());

        // Non-existent roledelete: should 404 or 422, never 500
        $res4 = $this->postJson('/roledelete', ['id' => 99999999]);
        $this->assertNotEquals(500, $res4->getStatusCode());
    }

    /**
     * Requirement R2.11: Admin role cannot be renamed or stripped of permissions
     */
    public function test_admin_role_cannot_be_renamed_or_stripped_of_permissions(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();

        // 1. Attempt to rename Admin
        $renameResponse = $this->actingAs($this->adminUser)->postJson('/roleupdate', [
            'id' => $adminRole->id,
            'name' => 'Renamed Admin ' . uniqid(),
            'permissions' => ['customer.view'],
        ]);

        $renameResponse->assertStatus(200);
        $renameResponse->assertJson([
            'status' => 0,
            'message' => 'Default Administrator role cannot be renamed.',
        ]);

        // 2. Attempt to strip permissions: Admin retains all permissions
        $stripResponse = $this->actingAs($this->adminUser)->postJson('/roleupdate', [
            'id' => $adminRole->id,
            'name' => 'Admin',
            'permissions' => [],
        ]);

        $stripResponse->assertStatus(200);
        $stripResponse->assertJson([
            'status' => 1,
            'message' => 'Administrator role permissions preserved with full system access.',
        ]);

        $adminRole->refresh();
        $this->assertEquals('Admin', $adminRole->name);
        $this->assertGreaterThanOrEqual(50, $adminRole->permissions()->count());
    }

    /**
     * Requirement R2.12: Cannot delete a role if active users are assigned to it
     */
    public function test_role_cannot_be_deleted_if_active_users_are_assigned(): void
    {
        $role = Role::create([
            'name' => 'Active Officer Role ' . uniqid(),
            'guard_name' => 'web',
        ]);

        // Assign a user to this role via role_id
        $assignedUser = User::create([
            'name' => 'Assigned Officer',
            'email' => 'assigned_' . uniqid() . '@gtms.com',
            'password' => bcrypt('secret123'),
            'role_id' => $role->id,
            'branch_id' => $this->adminUser->branch_id ?? 1,
            'status' => 1,
        ]);
        $assignedUser->assignRole($role);

        // Attempt deletion
        $response = $this->actingAs($this->adminUser)->postJson('/roledelete', [
            'id' => $role->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 0,
        ]);
        $this->assertStringContainsString('currently assigned', $response->json('message'));
        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    /**
     * Requirement R2.13: Newly expanded CRUD permissions for PPT, DGPS, Drone, EC Certificate & EC Compliance
     */
    public function test_expanded_crud_permissions_can_be_assigned(): void
    {
        $expandedPerms = [
            'ppt.create', 'ppt.edit', 'ppt.delete',
            'dgps.create', 'dgps.edit', 'dgps.delete',
            'drone.create', 'drone.edit', 'drone.delete',
            'ec_certificate.create', 'ec_certificate.edit', 'ec_certificate.delete',
            'ec_compliance.create', 'ec_compliance.edit', 'ec_compliance.delete',
        ];

        $roleName = 'Technical Specialist ' . uniqid();
        $response = $this->actingAs($this->adminUser)->postJson('/roleadd', [
            'name' => $roleName,
            'permissions' => $expandedPerms,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 1]);

        $createdRole = Role::where('name', $roleName)->first();
        $this->assertNotNull($createdRole);
        $this->assertCount(count($expandedPerms), $createdRole->permissions);

        foreach ($expandedPerms as $perm) {
            $this->assertTrue($createdRole->hasPermissionTo($perm));
        }
    }
}

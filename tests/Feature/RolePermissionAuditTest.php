<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RolePermissionAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $staffUser;
    protected User $officerUser;
    protected Role $adminRole;
    protected Role $superAdminRole;
    protected Role $staffRole;
    protected Role $officerRole;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->branch = Branch::firstOrCreate(
            ['branch_name' => 'Audit Test Branch'],
            [
                'contact_person' => 'Branch Manager',
                'mobile' => '9876543210',
                'address' => '123 Test Road',
                'city' => 'Salem',
                'state' => 'Tamil Nadu',
                'pincode' => '636001',
                'status' => 1,
            ]
        );

        $this->adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $this->superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $this->staffRole = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $this->officerRole = Role::firstOrCreate(['name' => 'Officer', 'guard_name' => 'web']);

        $this->adminUser = User::firstOrCreate(
            ['email' => 'admin@gtms.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('admin123'),
                'show_password' => 'admin123',
                'role_id' => $this->adminRole->id,
                'branch_id' => $this->branch->id,
                'status' => 1,
                'user_code' => 'LUK_001',
            ]
        );
        $this->adminUser->syncRoles(['Admin']);

        $this->staffUser = User::create([
            'name' => 'Staff Member ' . uniqid(),
            'email' => 'staff_' . uniqid() . '@gtms.com',
            'password' => bcrypt('staff123'),
            'show_password' => 'staff123',
            'role_id' => $this->staffRole->id,
            'branch_id' => $this->branch->id,
            'status' => 1,
            'user_code' => 'LUK_' . rand(500, 999),
        ]);
        $this->staffUser->syncRoles(['Staff']);

        $this->officerUser = User::create([
            'name' => 'Field Officer ' . uniqid(),
            'email' => 'officer_' . uniqid() . '@gtms.com',
            'password' => bcrypt('officer123'),
            'show_password' => 'officer123',
            'role_id' => $this->officerRole->id,
            'branch_id' => $this->branch->id,
            'status' => 1,
            'user_code' => 'LUK_' . rand(500, 999),
        ]);
        $this->officerUser->syncRoles(['Officer']);
    }

    public function test_all_system_permissions_exist_in_database()
    {
        $expectedPermissions = [
            'dashboard.view',
            'branch.view', 'branch.create', 'branch.edit', 'branch.delete',
            'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'customer.view', 'customer.create', 'customer.edit', 'customer.delete',
            'application.view', 'application.create', 'application.edit', 'application.delete',
            'mining.view', 'mining.create', 'mining.edit', 'mining.delete',
            'environment.view', 'environment.create', 'environment.edit', 'environment.delete',
            'environment.b1.view', 'environment.b2.view', 'environment.b2.create', 'environment.b2.upload', 'environment.b2.review', 'environment.b2.status',
            'ec_certificate.view', 'ec_certificate.create', 'ec_certificate.edit', 'ec_certificate.delete', 'ec_certificate.manage',
            'ec_compliance.view', 'ec_compliance.create', 'ec_compliance.edit', 'ec_compliance.delete', 'ec_compliance.manage',
            'ppt.view', 'ppt.create', 'ppt.edit', 'ppt.delete', 'ppt.manage',
            'dgps.view', 'dgps.create', 'dgps.edit', 'dgps.delete', 'dgps.manage',
            'drone.view', 'drone.create', 'drone.edit', 'drone.delete', 'drone.manage',
            'account.view', 'account.create', 'account.edit', 'account.delete',
            'notification.view', 'notification.manage',
            'category.view', 'category.create', 'category.edit', 'category.delete',
            'product.view', 'product.create', 'product.edit', 'product.delete',
            'unit.view', 'unit.create', 'unit.edit', 'unit.delete',
        ];

        $missing = [];
        foreach ($expectedPermissions as $perm) {
            if (!Permission::where('name', $perm)->exists()) {
                $missing[] = $perm;
            }
        }

        $this->assertEmpty($missing, 'Permissions missing from database: ' . implode(', ', $missing));
    }

    public function test_admin_and_super_admin_roles_have_all_permissions()
    {
        $totalPermissions = Permission::count();
        $this->assertGreaterThanOrEqual(78, $totalPermissions);

        $adminPerms = $this->adminRole->permissions()->count();
        $this->assertEquals($totalPermissions, $adminPerms, 'Admin must have all system permissions');

        $superAdminPerms = $this->superAdminRole->permissions()->count();
        $this->assertEquals($totalPermissions, $superAdminPerms, 'Super Admin must have all system permissions');
    }

    public function test_officer_and_staff_roles_have_configured_permissions()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $this->officerRole->refresh();
        $this->staffRole->refresh();

        $this->assertTrue($this->officerRole->hasPermissionTo('customer.create'));
        $this->assertTrue($this->officerRole->hasPermissionTo('application.create'));
        $this->assertTrue($this->officerRole->hasPermissionTo('mining.create'));
        $this->assertTrue($this->officerRole->hasPermissionTo('dgps.create'));
        $this->assertTrue($this->officerRole->hasPermissionTo('drone.create'));
        $this->assertTrue($this->officerRole->hasPermissionTo('unit.create'));

        $this->assertTrue($this->staffRole->hasPermissionTo('dashboard.view'));
        $this->assertTrue($this->staffRole->hasPermissionTo('customer.view'));
        $this->assertTrue($this->staffRole->hasPermissionTo('application.view'));
        $this->assertTrue($this->staffRole->hasPermissionTo('account.view'));
        $this->assertTrue($this->staffRole->hasPermissionTo('unit.view'));
        $this->assertFalse($this->staffRole->hasPermissionTo('users.delete'));
        $this->assertFalse($this->staffRole->hasPermissionTo('roles.delete'));
    }

    public function test_admin_and_super_admin_cannot_be_renamed_or_deleted()
    {
        // Attempt to rename Admin
        $respRenameAdmin = $this->actingAs($this->adminUser)->postJson(route('roleupdate'), [
            'id' => $this->adminRole->id,
            'name' => 'Renamed Admin',
        ]);
        $respRenameAdmin->assertJson([
            'status' => 0,
            'message' => 'Default Administrator role cannot be renamed.',
        ]);

        // Attempt to rename Super Admin
        $respRenameSuperAdmin = $this->actingAs($this->adminUser)->postJson(route('roleupdate'), [
            'id' => $this->superAdminRole->id,
            'name' => 'Renamed Super Admin',
        ]);
        $respRenameSuperAdmin->assertJson([
            'status' => 0,
            'message' => 'Default Administrator role cannot be renamed.',
        ]);

        // Attempt to delete Admin
        $respDeleteAdmin = $this->actingAs($this->adminUser)->postJson(route('roledelete'), [
            'id' => $this->adminRole->id,
        ]);
        $respDeleteAdmin->assertJson([
            'status' => 0,
            'message' => 'Default Administrator role cannot be deleted.',
        ]);

        // Attempt to delete Super Admin
        $respDeleteSuperAdmin = $this->actingAs($this->adminUser)->postJson(route('roledelete'), [
            'id' => $this->superAdminRole->id,
        ]);
        $respDeleteSuperAdmin->assertJson([
            'status' => 0,
            'message' => 'Default Administrator role cannot be deleted.',
        ]);
    }

    public function test_can_create_custom_role_with_granular_permissions_and_retrieve_via_ajax()
    {
        $roleName = 'Inspector_' . uniqid();
        $perms = ['customer.view', 'customer.create', 'unit.view', 'notification.view'];

        $resp = $this->actingAs($this->adminUser)->postJson(route('roleadd'), [
            'name' => $roleName,
            'permissions' => $perms,
        ]);

        $resp->assertJson(['status' => 1, 'message' => 'Role Added Successfully']);
        $newRole = Role::where('name', $roleName)->first();
        $this->assertNotNull($newRole);

        // Retrieve permissions via getPermissions endpoint
        $getPermsResp = $this->actingAs($this->adminUser)->getJson(route('roles.permissions', $newRole->id));
        $getPermsResp->assertJson(['status' => 1]);
        $retrievedPerms = $getPermsResp->json('permissions');
        sort($perms);
        sort($retrievedPerms);
        $this->assertEquals($perms, $retrievedPerms);
    }

    public function test_permission_middleware_gates_routes_properly()
    {
        // 1. Staff cannot access roles page
        $this->actingAs($this->staffUser)->get(route('roles.index'))->assertStatus(403);

        // 2. Admin can access roles page
        $this->actingAs($this->adminUser)->get(route('roles.index'))->assertStatus(200);

        // 3. User with no permissions cannot access customer directory
        $bareUser = User::create([
            'name' => 'Bare User ' . uniqid(),
            'email' => 'bare_' . uniqid() . '@gtms.com',
            'password' => bcrypt('password123'),
            'branch_id' => $this->branch->id,
            'status' => 1,
            'user_code' => 'LUK_' . rand(500, 999),
        ]);

        $this->actingAs($bareUser)->get(route('customers.index'))->assertStatus(403);
        $this->actingAs($bareUser)->get(route('customers.lookup.mimas', 'MIMAS-TEST-123'))->assertStatus(403);

        // 4. Giving bareUser customer.view allows access
        $bareUser->givePermissionTo('customer.view');
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->actingAs($bareUser)->get(route('customers.index'))->assertStatus(200);
        $lookupResp = $this->actingAs($bareUser)->get(route('customers.lookup.mimas', 'NON_EXISTENT'));
        $lookupResp->assertStatus(404); // Reached controller action! Returns 404 not found JSON instead of 403 Forbidden
        $lookupResp->assertJson(['status' => 0]);
    }
}

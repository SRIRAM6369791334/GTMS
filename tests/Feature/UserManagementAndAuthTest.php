<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class UserManagementAndAuthTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $staffUser;
    protected Role $adminRole;
    protected Role $staffRole;
    protected Role $officerRole;
    protected Branch $branch;
    protected array $filesToClean = [];

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Roles setup
        $this->adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $this->staffRole = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
        $this->officerRole = Role::firstOrCreate(['name' => 'Officer', 'guard_name' => 'web']);

        // 2. Branch setup
        $this->branch = Branch::firstOrCreate(
            ['branch_name' => 'Test Branch Office'],
            [
                'contact_person' => 'Branch Manager',
                'mobile' => '9876543210',
                'address' => '123 Mining Road',
                'city' => 'Salem',
                'state' => 'Tamil Nadu',
                'pincode' => '636001',
                'status' => 1,
            ]
        );

        // 3. Admin user setup
        $admin = User::where('email', 'admin@gtms.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@gtms.com',
                'password' => bcrypt('admin123'),
                'show_password' => 'admin123',
                'role_id' => $this->adminRole->id,
                'branch_id' => $this->branch->id,
                'status' => 1,
                'user_code' => 'LUK_001',
            ]);
        }
        $admin->syncRoles(['Admin']);
        $this->adminUser = $admin;

        // 4. Staff user setup
        $staff = User::where('email', 'staff_unit_test@gtms.com')->first();
        if (!$staff) {
            $staff = User::create([
                'name' => 'Staff Test Member',
                'email' => 'staff_unit_test@gtms.com',
                'password' => bcrypt('staff123'),
                'show_password' => 'staff123',
                'role_id' => $this->staffRole->id,
                'branch_id' => $this->branch->id,
                'status' => 1,
                'user_code' => 'LUK_002',
            ]);
        }
        $staff->syncRoles(['Staff']);
        $this->staffUser = $staff;
    }

    protected function tearDown(): void
    {
        foreach ($this->filesToClean as $filePath) {
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        parent::tearDown();
    }

    /**
     * Requirement R3.1: Guest access to login view and redirecting authenticated user to dashboard
     */
    public function test_guest_can_access_login_views_and_auth_user_redirects_to_dashboard(): void
    {
        // Guests can view / and /login
        $this->get('/')->assertStatus(200)->assertViewIs('pages.login');
        $this->get('/login')->assertStatus(200)->assertViewIs('pages.login');

        // Authenticated user is redirected to dashboard
        $this->actingAs($this->adminUser)->get('/login')->assertRedirect('/dashboard');
        $this->actingAs($this->adminUser)->get('/')->assertRedirect('/dashboard');
    }

    /**
     * Requirement R3.2: Dual-identifier login via canonical email
     */
    public function test_user_can_login_via_canonical_email(): void
    {
        $testEmail = 'user_login_' . uniqid() . '@example.com';
        $user = User::create([
            'name' => 'Login Email User',
            'email' => $testEmail,
            'password' => bcrypt('Password@123'),
            'show_password' => 'Password@123',
            'role_id' => $this->staffRole->id,
            'branch_id' => $this->branch->id,
            'status' => 1,
            'user_code' => 'LUK_EM_' . uniqid(),
        ]);

        $response = $this->post('/login', [
            'email' => $testEmail,
            'password' => 'Password@123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    /**
     * Requirement R3.2: Dual-identifier login via user_code (e.g. LUK_001)
     */
    public function test_user_can_login_via_user_code_identifier(): void
    {
        $code = 'LUK_CD_' . uniqid();
        $user = User::create([
            'name' => 'Login Code User',
            'email' => 'code_user_' . uniqid() . '@example.com',
            'password' => bcrypt('CodePass#123'),
            'show_password' => 'CodePass#123',
            'role_id' => $this->staffRole->id,
            'branch_id' => $this->branch->id,
            'status' => 1,
            'user_code' => $code,
        ]);

        // Submit user_code in 'email' form field
        $response = $this->post('/login', [
            'email' => $code,
            'password' => 'CodePass#123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    /**
     * Requirement R3.3: Inactive account login rejection (status != 1)
     */
    public function test_inactive_account_login_is_rejected_with_exact_message(): void
    {
        $inactiveEmail = 'inactive_' . uniqid() . '@example.com';
        $inactiveCode = 'LUK_IN_' . uniqid();
        User::create([
            'name' => 'Inactive User',
            'email' => $inactiveEmail,
            'password' => bcrypt('Secret@123'),
            'show_password' => 'Secret@123',
            'role_id' => $this->staffRole->id,
            'branch_id' => $this->branch->id,
            'status' => 0, // Inactive
            'user_code' => $inactiveCode,
        ]);

        // 1. Attempt with email
        $responseEmail = $this->from('/login')->post('/login', [
            'email' => $inactiveEmail,
            'password' => 'Secret@123',
        ]);

        $responseEmail->assertRedirect('/login');
        $responseEmail->assertSessionHasErrors([
            'email' => 'Your account is inactive. Please contact administrator.',
        ]);
        $this->assertFalse(Auth::check());

        // 2. Attempt with user_code
        $responseCode = $this->from('/login')->post('/login', [
            'email' => $inactiveCode,
            'password' => 'Secret@123',
        ]);

        $responseCode->assertRedirect('/login');
        $responseCode->assertSessionHasErrors([
            'email' => 'Your account is inactive. Please contact administrator.',
        ]);
        $this->assertFalse(Auth::check());
    }

    /**
     * Requirement R3.4: Unregistered identifier and incorrect password error handling with proper field key isolation
     */
    public function test_login_field_key_isolation_on_errors(): void
    {
        // 1. Unregistered identifier: error isolated under key 'email'
        $resNonExistent = $this->from('/login')->post('/login', [
            'email' => 'unregistered_' . uniqid() . '@gtms.com',
            'password' => 'anypassword',
        ]);
        $resNonExistent->assertRedirect('/login');
        $resNonExistent->assertSessionHasErrors([
            'email' => 'No account found with this email or User ID.',
        ]);
        $resNonExistent->assertSessionMissing('errors.password');

        // 2. Existing user with wrong password: error isolated under key 'password'
        $resBadPass = $this->from('/login')->post('/login', [
            'email' => $this->adminUser->email,
            'password' => 'completely_wrong_pass',
        ]);
        $resBadPass->assertRedirect('/login');
        $resBadPass->assertSessionHasErrors([
            'password' => 'Incorrect password entered.',
        ]);
        $resBadPass->assertSessionMissing('errors.email');
    }

    /**
     * Requirement R3.5: User logout invalidates session, regenerates CSRF token, and redirects
     */
    public function test_user_logout_invalidates_session_and_redirects(): void
    {
        $this->actingAs($this->adminUser);
        $this->assertTrue(Auth::check());

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Logged out successfully.');
        $this->assertFalse(Auth::check());
    }

    /**
     * Requirement R3.6: User directory view rendering for authorized users (users.view)
     */
    public function test_user_directory_view_renders_for_authorized_users(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/user');

        $response->assertStatus(200);
        $response->assertViewIs('pages.authentication.users.index');
        $response->assertViewHasAll(['users', 'role', 'branch']);
        $response->assertSee('Total Users');

        $users = $response->viewData('users');
        $this->assertNotEmpty($users);
    }

    /**
     * Requirement R3.7: User provisioning with automatic user_code generation (LUK_ padded to 3 digits)
     */
    public function test_user_provisioning_generates_user_code_automatically(): void
    {
        $email = 'prov_' . uniqid() . '@gtms.com';
        $payload = [
            'name' => 'Auto Code User',
            'email' => $email,
            'password' => 'securePass123',
            'role_id' => $this->staffRole->id,
            'branch_id' => $this->branch->id,
            'mobile_num' => '9840123456',
            'status' => 1,
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/useradd', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'message' => 'User Added Successfully',
        ]);

        $createdUser = User::where('email', $email)->first();
        $this->assertNotNull($createdUser);
        $expectedCode = 'LUK_' . str_pad($createdUser->id, 3, '0', STR_PAD_LEFT);
        $this->assertEquals($expectedCode, $createdUser->user_code);
        $this->assertEquals('securePass123', $createdUser->show_password);
        $this->assertEquals('9840123456', $createdUser->mobile_num);
    }

    /**
     * Requirement R3.8: Dual role synchronization on user creation (users.role_id and Spatie model_has_roles)
     */
    public function test_dual_role_synchronization_on_creation(): void
    {
        $email = 'dual_sync_' . uniqid() . '@gtms.com';
        $payload = [
            'name' => 'Dual Sync Provision User',
            'email' => $email,
            'password' => 'Pass@12345',
            'role_id' => $this->officerRole->id,
            'branch_id' => $this->branch->id,
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/useradd', $payload);
        $response->assertStatus(200);

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);

        // 1. Direct column role_id
        $this->assertEquals($this->officerRole->id, $user->role_id);

        // 2. Spatie role assignment in model_has_roles
        $this->assertTrue($user->hasRole('Officer'));
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $this->officerRole->id,
            'model_id' => $user->id,
            'model_type' => User::class,
        ]);
    }

    /**
     * Requirement R3.9: Avatar image uploading with valid mime and size validation
     */
    public function test_avatar_uploading_and_validation(): void
    {
        $uploadDir = public_path('uploads/users');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // 1. Successful upload of valid image
        $imageFile = UploadedFile::fake()->image('test_profile.png', 120, 120);
        $email = 'avatar_user_' . uniqid() . '@gtms.com';

        $response = $this->actingAs($this->adminUser)->postJson('/useradd', [
            'name' => 'Avatar Test User',
            'email' => $email,
            'password' => 'ValidPass123',
            'role_id' => $this->staffRole->id,
            'branch_id' => $this->branch->id,
            'image' => $imageFile,
        ]);

        $response->assertStatus(200);
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user->image);

        $diskPath = $uploadDir . '/' . $user->image;
        $this->filesToClean[] = $diskPath;
        $this->assertFileExists($diskPath);

        // 2. Validation failure on non-image file
        $pdfFile = UploadedFile::fake()->create('hacker.pdf', 500, 'application/pdf');
        $badRes = $this->actingAs($this->adminUser)->postJson('/useradd', [
            'name' => 'Bad Image User',
            'email' => 'bad_image_' . uniqid() . '@gtms.com',
            'password' => 'ValidPass123',
            'role_id' => $this->staffRole->id,
            'branch_id' => $this->branch->id,
            'image' => $pdfFile,
        ]);

        $badRes->assertStatus(422);
        $badRes->assertJsonValidationErrors(['image']);
    }

    /**
     * Requirement R3.10: User update with attribute modification, password preservation if omitted, and dual RBAC re-sync
     */
    public function test_user_update_with_password_preservation_and_rbac_re_sync(): void
    {
        $user = User::create([
            'name' => 'Pre-Update User',
            'email' => 'orig_attr_' . uniqid() . '@gtms.com',
            'password' => bcrypt('OriginalPass#1'),
            'show_password' => 'OriginalPass#1',
            'role_id' => $this->staffRole->id,
            'branch_id' => $this->branch->id,
            'status' => 1,
            'mobile_num' => '9840000000',
        ]);
        $user->syncRoles(['Staff']);

        $updatedEmail = 'updated_' . uniqid() . '@gtms.com';
        $updatePayload = [
            'id' => $user->id,
            'name' => 'Post-Update User',
            'email' => $updatedEmail,
            'role_id' => $this->officerRole->id,
            'branch_id' => $this->branch->id,
            'mobile_num' => '9841111111',
            'status' => 1,
            // password omitted to verify preservation
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/useredit', $updatePayload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'message' => 'User Updated Successfully',
        ]);

        $user->refresh();
        $this->assertEquals('Post-Update User', $user->name);
        $this->assertEquals($updatedEmail, $user->email);
        $this->assertEquals('9841111111', $user->mobile_num);
        $this->assertEquals('OriginalPass#1', $user->show_password); // Preserved

        // Dual RBAC re-sync verification
        $this->assertEquals($this->officerRole->id, $user->role_id);
        $this->assertTrue($user->hasRole('Officer'));
        $this->assertFalse($user->hasRole('Staff'));
    }

    /**
     * Requirement R3.11: Avatar replacement unlinks old avatar file from disk
     */
    public function test_avatar_replacement_unlinks_old_file(): void
    {
        $uploadDir = public_path('uploads/users');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Create initial fake avatar file
        $oldImageName = 'old_avatar_' . uniqid() . '.jpg';
        $oldImagePath = $uploadDir . '/' . $oldImageName;
        file_put_contents($oldImagePath, 'old avatar mock binary');
        $this->filesToClean[] = $oldImagePath;

        $user = User::create([
            'name' => 'Avatar Replacement User',
            'email' => 'replace_avatar_' . uniqid() . '@gtms.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->staffRole->id,
            'branch_id' => $this->branch->id,
            'image' => $oldImageName,
            'status' => 1,
        ]);

        $this->assertFileExists($oldImagePath);

        // Upload replacement image
        $replacementFile = UploadedFile::fake()->image('new_avatar.jpg', 150, 150);

        $response = $this->actingAs($this->adminUser)->postJson('/useredit', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_id' => $this->staffRole->id,
            'branch_id' => $this->branch->id,
            'image' => $replacementFile,
        ]);

        $response->assertStatus(200);
        $user->refresh();

        // Old file must be unlinked
        $this->assertFileDoesNotExist($oldImagePath);

        // New file must exist
        $newImagePath = $uploadDir . '/' . $user->image;
        $this->filesToClean[] = $newImagePath;
        $this->assertFileExists($newImagePath);
    }

    /**
     * Requirement R3.12: Self-deletion prevention guard
     */
    public function test_self_deletion_is_prevented(): void
    {
        $response = $this->actingAs($this->adminUser)->postJson('/userdelete', [
            'id' => $this->adminUser->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 0,
            'message' => 'You cannot delete your own account.',
        ]);

        $this->assertDatabaseHas('users', ['id' => $this->adminUser->id]);
    }

    /**
     * Requirement R3.13: Last-admin deletion protection guard
     */
    public function test_last_admin_account_cannot_be_deleted(): void
    {
        // 1. Ensure only 1 user currently holds 'Admin' role
        $otherAdmins = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->where('id', '!=', $this->adminUser->id)->get();
        foreach ($otherAdmins as $otherAdmin) {
            $otherAdmin->removeRole('Admin');
        }
        $this->assertEquals(1, User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->count());

        // 2. Create another user who has users.delete permission
        $officerWithDelete = User::create([
            'name' => 'Officer Deleter',
            'email' => 'deleter_' . uniqid() . '@gtms.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->officerRole->id,
            'branch_id' => $this->branch->id,
            'status' => 1,
        ]);
        $officerWithDelete->givePermissionTo('users.delete');

        // 3. Officer attempts to delete the last Admin account
        $response = $this->actingAs($officerWithDelete)->postJson('/userdelete', [
            'id' => $this->adminUser->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 0,
            'message' => 'The last Admin account cannot be deleted.',
        ]);

        $this->assertDatabaseHas('users', ['id' => $this->adminUser->id]);

        // 4. If a second Admin exists, deletion of one Admin succeeds
        $secondAdmin = User::create([
            'name' => 'Second Admin User',
            'email' => 'admin2_' . uniqid() . '@gtms.com',
            'password' => bcrypt('admin2Pass!'),
            'role_id' => $this->adminRole->id,
            'branch_id' => $this->branch->id,
            'status' => 1,
        ]);
        $secondAdmin->syncRoles(['Admin']);
        $this->assertGreaterThan(1, User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->count());

        $delAdminRes = $this->actingAs($this->adminUser)->postJson('/userdelete', [
            'id' => $secondAdmin->id,
        ]);
        $delAdminRes->assertStatus(200);
        $delAdminRes->assertJson([
            'status' => 1,
            'message' => 'User Deleted Successfully',
        ]);
        $this->assertDatabaseMissing('users', ['id' => $secondAdmin->id]);
    }

    /**
     * Requirement R3.14: User deletion succeeds, unlinks avatar from disk, and removes user record
     */
    public function test_user_deletion_succeeds_and_unlinks_avatar(): void
    {
        $uploadDir = public_path('uploads/users');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $avatarName = 'user_to_delete_' . uniqid() . '.jpg';
        $avatarPath = $uploadDir . '/' . $avatarName;
        file_put_contents($avatarPath, 'sample binary image content');
        $this->filesToClean[] = $avatarPath;

        $deletableUser = User::create([
            'name' => 'Deletable Target User',
            'email' => 'target_' . uniqid() . '@gtms.com',
            'password' => bcrypt('password123'),
            'role_id' => $this->staffRole->id,
            'branch_id' => $this->branch->id,
            'image' => $avatarName,
            'status' => 1,
        ]);
        $deletableUser->syncRoles(['Staff']);

        $this->assertFileExists($avatarPath);

        $response = $this->actingAs($this->adminUser)->postJson('/userdelete', [
            'id' => $deletableUser->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'message' => 'User Deleted Successfully',
        ]);

        $this->assertDatabaseMissing('users', ['id' => $deletableUser->id]);
        $this->assertFileDoesNotExist($avatarPath);
    }

    /**
     * Requirement R3.15: Permission middleware gating across CRUD routes (users.view, users.create, users.edit, users.delete)
     */
    public function test_permission_middleware_gating_across_user_routes(): void
    {
        // 1. Guest access is redirected to login
        $this->get('/user')->assertRedirect(route('login'));
        $this->post('/useradd', ['name' => 'Unauth'])->assertRedirect(route('login'));
        $this->post('/useredit', ['id' => 1])->assertRedirect(route('login'));
        $this->post('/userdelete', ['id' => 1])->assertRedirect(route('login'));

        // 2. User with no permissions is forbidden (403)
        $noPermsUser = User::create([
            'name' => 'No Perms User',
            'email' => 'noperms_' . uniqid() . '@gtms.com',
            'password' => bcrypt('password123'),
            'status' => 1,
        ]);
        $this->actingAs($noPermsUser);

        $this->get('/user')->assertStatus(403);
        $this->post('/useradd', ['name' => 'Forbidden'])->assertStatus(403);
        $this->post('/useredit', ['id' => 1])->assertStatus(403);
        $this->post('/userdelete', ['id' => 1])->assertStatus(403);

        // 3. User with users.view can view directory but cannot mutate
        $viewUser = User::create([
            'name' => 'View Only User',
            'email' => 'viewonly_' . uniqid() . '@gtms.com',
            'password' => bcrypt('password123'),
            'status' => 1,
        ]);
        $viewUser->givePermissionTo('users.view');
        $this->actingAs($viewUser);

        $this->get('/user')->assertStatus(200);
        $this->post('/useradd', ['name' => 'Forbidden'])->assertStatus(403);
        $this->post('/useredit', ['id' => 1])->assertStatus(403);
        $this->post('/userdelete', ['id' => 1])->assertStatus(403);
    }

    /**
     * Requirement R3.16: Zero unhandled 500 exceptions across all routes
     */
    public function test_zero_unhandled_500_exceptions_across_user_and_auth_endpoints(): void
    {
        $this->actingAs($this->adminUser);

        // 1. Invalid payload on /useradd: returns 422, not 500
        $resAdd = $this->postJson('/useradd', ['invalid_key' => 'garbage']);
        $this->assertNotEquals(500, $resAdd->getStatusCode());
        $this->assertEquals(422, $resAdd->getStatusCode());

        // 2. Invalid payload on /useredit: returns 422, not 500
        $resEdit = $this->postJson('/useredit', ['id' => 99999999]);
        $this->assertNotEquals(500, $resEdit->getStatusCode());
        $this->assertEquals(422, $resEdit->getStatusCode());

        // 3. Invalid payload on /userdelete: returns 422, not 500
        $resDel = $this->postJson('/userdelete', ['id' => 99999999]);
        $this->assertNotEquals(500, $resDel->getStatusCode());
        $this->assertEquals(422, $resDel->getStatusCode());
    }
}

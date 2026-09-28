# Handoff Report: Survey of Roles & Permissions Module (R2)

**Agent:** `explorer_survey_roles`  
**Working Directory:** `C:\xampp\htdocs\GTMS\gtms\.agents\teamwork\explorer_survey_roles`  
**Target Milestone:** R2 — Comprehensive Feature Test Suite for Roles and Permissions  
**Target Codebase:** `c:\xampp\htdocs\GTMS\gtms`  
**Timestamp:** 2026-09-28T06:15:00Z  

---

## 1. Observation

### 1.1 Complete Catalog of Roles Routes (`routes/web.php`)
All role-related routes are defined within the authenticated route group `Route::middleware('auth')->group(...)` in `routes/web.php:126-134`:

| Route Name | HTTP Verb | URI | Controller Action | Middleware Pipeline | Operational Purpose |
| :--- | :---: | :--- | :--- | :--- | :--- |
| `roles.index` | `GET` | `/roles` | `App\Http\Controllers\RolesController@index` | `web`, `auth`, `permission:roles.view` | Renders role management dashboard, roles matrix, permission counts, and modals |
| `roles.permissions` | `GET` | `roles/{id}/permissions` | `App\Http\Controllers\RolesController@getPermissions` | `web`, `auth`, `permission:roles.view` | AJAX endpoint returning JSON array of assigned permission names for a role |
| `roleadd` | `POST` | `roleadd` | `App\Http\Controllers\RolesController@store` | `web`, `auth`, `permission:roles.create` | AJAX endpoint to create role and synchronize assigned permissions |
| `roleupdate` | `POST` | `roleupdate` | `App\Http\Controllers\RolesController@update` | `web`, `auth`, `permission:roles.edit` | AJAX endpoint to update role name and synchronize updated permissions |
| `roledelete` | `POST` | `roledelete` | `App\Http\Controllers\RolesController@destroy` | `web`, `auth`, `permission:roles.delete` | AJAX endpoint to delete custom roles with safety block on Admin/Super Admin |

*Code Reference (`routes/web.php:126-134`):*
```php
126:     // Role routes
127:     Route::middleware('permission:roles.view')->group(function () {
128:         Route::get('/roles', [RolesController::class, 'index'])->name('roles.index');
129:         Route::get('roles/{id}/permissions', [RolesController::class, 'getPermissions'])->name('roles.permissions');
130:     });
131:     Route::post('roleadd', [RolesController::class, 'store'])->name('roleadd')->middleware('permission:roles.create');
132:     Route::post('roleupdate', [RolesController::class, 'update'])->name('roleupdate')->middleware('permission:roles.edit');
133:     Route::post('roledelete', [RolesController::class, 'destroy'])->name('roledelete')->middleware('permission:roles.delete');
```

---

### 1.2 Method-by-Method Audit of `RolesController.php`
`app/Http/Controllers/RolesController.php` contains 130 lines spanning 5 core controller methods:

#### Method 1: `index()` (`RolesController.php:12-43`)
* **Signature:** `public function index()`
* **Data Retrieval:**
  - `Role::with('permissions')->get()`: Eager loads the `permissions` relationship to avoid N+1 queries.
  - `Permission::orderBy('name')->get()`: Retrieves all 48 permissions sorted alphabetically.
* **Module Grouping Algorithm (`RolesController.php:18-40`):**
  Defines 14 functional module labels:
  - `dashboard` => `'Dashboard'`
  - `branch` => `'Department / Branch'`
  - `roles` => `'Roles & Permissions'`
  - `users` => `'Users Management'`
  - `application` => `'Lease Applications'`
  - `mining` => `'Mining Plan'`
  - `environment` => `'Environment Clearance'`
  - `ec_certificate` => `'EC Certificate'`
  - `ppt` => `'PPT Department'`
  - `dgps` => `'DGPS Survey'`
  - `drone` => `'Drone Survey'`
  - `category` => `'Categories'`
  - `product` => `'Products'`
  - `unit` => `'Units'`
  Splits permission names by `.` (`$parts = explode('.', $perm->name)`), extracting `$parts[0]` as the module key. E.g., `environment.b1.view` and `environment.b2.create` are grouped under `'environment'`.
* **Output:** Returns view `pages.authentication.roles.index` with `compact('roles', 'groupedPermissions', 'modules')`.

#### Method 2: `store(Request $request)` (`RolesController.php:45-69`)
* **Signature:** `public function store(Request $request)`
* **Validation Rules (`RolesController.php:47-50`):**
  - `'name' => 'required|unique:roles,name'`
  - `'permissions' => 'nullable|array'`
* **Execution Flow:**
  - Creates role: `Role::create(['name' => $request->name, 'guard_name' => 'web']);`
  - If `$request->filled('permissions')`: `$role->syncPermissions($request->permissions);`
  - Cache Invalidation: `app()[PermissionRegistrar::class]->forgetCachedPermissions();`
  - Eager reload: `$role->load('permissions');`
* **Response Format:**
  ```json
  {
    "status": 1,
    "message": "Role Added Successfully",
    "data": { ...role object with loaded permissions... }
  }
  ```

#### Method 3: `getPermissions($id)` (`RolesController.php:71-79`)
* **Signature:** `public function getPermissions($id)`
* **Execution Flow:**
  - Queries: `$role = Role::with('permissions')->findOrFail($id);`
  - Extracts permission names: `$role->permissions->pluck('name')->toArray()`
* **Response Format:**
  ```json
  {
    "status": 1,
    "role": { ...role model... },
    "permissions": ["dashboard.view", "branch.view", ...]
  }
  ```
* **Failure Mode:** If `$id` does not exist, `findOrFail` throws `ModelNotFoundException` returning HTTP 404.

#### Method 4: `update(Request $request)` (`RolesController.php:81-107`)
* **Signature:** `public function update(Request $request)`
* **Validation Rules (`RolesController.php:83-87`):**
  - `'id' => 'required|exists:roles,id'`
  - `'name' => 'required|unique:roles,name,' . $request->id`
  - `'permissions' => 'nullable|array'`
* **Execution Flow:**
  - Finds role: `$role = Role::findOrFail($request->id);`
  - Updates role name: `$role->name = $request->name; $role->save();`
  - Sync permissions:
    ```php
    if ($request->has('permissions')) {
        $role->syncPermissions($request->permissions ?? []);
    } else {
        $role->syncPermissions([]);
    }
    ```
    If `permissions` is omitted in payload (e.g., when all checkboxes are deselected), it correctly synchronizes `[]`, revoking all permissions.
  - Cache Invalidation: `app()[PermissionRegistrar::class]->forgetCachedPermissions();`
  - Eager reload: `$role->load('permissions');`
* **Response Format:**
  ```json
  {
    "status": 1,
    "message": "Role Updated Successfully",
    "data": { ...role object with permissions... }
  }
  ```

#### Method 5: `destroy(Request $request)` (`RolesController.php:109-127`)
* **Signature:** `public function destroy(Request $request)`
* **Destruction Safeguards (`RolesController.php:111-117`):**
  ```php
  $role = Role::findOrFail($request->id);
  if ($role->name === 'Admin' || $role->name === 'Super Admin') {
      return response()->json([
          'status' => 0,
          'message' => 'Default Administrator role cannot be deleted.',
      ]);
  }
  ```
  Returns `{ "status": 0, "message": "Default Administrator role cannot be deleted." }` without executing delete.
* **Deletion Execution:**
  - `$role->delete();`
  - `app()[PermissionRegistrar::class]->forgetCachedPermissions();`
* **Response Format:**
  ```json
  {
    "status": 1,
    "message": "Role Deleted Successfully",
    "data": { ...deleted role object... }
  }
  ```

---

### 1.3 Spatie RBAC & Seeder Analysis (`RolePermissionSeeder.php`)

#### All 48 System Permissions Catalog
`database/seeders/RolePermissionSeeder.php:25-97` defines and seeds exactly **48 permissions** under guard `web`:

```
1.  dashboard.view
2.  branch.view
3.  branch.create
4.  branch.edit
5.  branch.delete
6.  roles.view
7.  roles.create
8.  roles.edit
9.  roles.delete
10. users.view
11. users.create
12. users.edit
13. users.delete
14. customer.view
15. customer.create
16. customer.edit
17. customer.delete
18. application.view
19. application.create
20. application.edit
21. application.delete
22. mining.view
23. mining.create
24. mining.edit
25. mining.delete
26. environment.view
27. environment.b1.view
28. environment.b2.view
29. environment.b2.create
30. environment.b2.upload
31. environment.b2.review
32. environment.b2.status
33. ec_certificate.view
34. ppt.view
35. ppt.manage
36. dgps.view
37. dgps.manage
38. drone.view
39. drone.manage
40. category.view
41. category.create
42. category.edit
43. category.delete
44. product.view
45. product.create
46. product.edit
47. product.delete
48. unit.view
```

#### Default Roles and Seeded Permissions Matrix
`RolePermissionSeeder.php:106-160` configures three standard organizational roles:

| Module / Domain | Total Perms | Admin (`RolePermissionSeeder:107-111`) | Officer (`RolePermissionSeeder:133-160`) | Staff (`RolePermissionSeeder:114-130`) |
| :--- | :---: | :---: | :---: | :---: |
| **Dashboard** | 1 | `dashboard.view` | `dashboard.view` | `dashboard.view` |
| **Branch / Dept** | 4 | `branch.*` (4 perms) | ❌ | ❌ |
| **Roles & Permissions** | 4 | `roles.*` (4 perms) | ❌ | ❌ |
| **User Management** | 4 | `users.*` (4 perms) | ❌ | `users.view` |
| **Customer Master** | 4 | `customer.*` (4 perms) | `view`, `create`, `edit` (3 perms) | `customer.view` |
| **Lease Application** | 4 | `application.*` (4 perms) | `view`, `create`, `edit` (3 perms) | `application.view` |
| **Mining Plan** | 4 | `mining.*` (4 perms) | `view`, `create`, `edit` (3 perms) | `mining.view` |
| **Environment Clearance**| 7 | `environment.*` (7 perms) | `view`, `b2.view`, `b2.create`, `b2.upload`, `b2.review` (5 perms) | `view`, `b2.view` (2 perms) |
| **EC Certificate** | 1 | `ec_certificate.view` | `ec_certificate.view` | `ec_certificate.view` |
| **PPT Department** | 2 | `ppt.view`, `ppt.manage` | `ppt.view`, `ppt.manage` | `ppt.view` |
| **DGPS Survey** | 2 | `dgps.view`, `dgps.manage` | `dgps.view`, `dgps.manage` | `dgps.view` |
| **Drone Survey** | 2 | `drone.view`, `drone.manage` | `drone.view`, `drone.manage` | `drone.view` |
| **Categories Master** | 4 | `category.*` (4 perms) | ❌ | ❌ |
| **Products Master** | 4 | `product.*` (4 perms) | ❌ | ❌ |
| **Units Master** | 1 | `unit.view` | ❌ | ❌ |
| **TOTAL PERMISSIONS** | **48** | **48 (100%)** | **22 (45.8%)** | **11 (22.9%)** |

#### Dual-Role & Super Admin Architecture
1. **The Seeded Super Admin User:**
   - In `RolePermissionSeeder.php:177-198`: User with `name => 'Super Admin'`, `email => 'admin@gtms.com'`, `user_code => 'LUK_001'`.
   - Assigned direct column `users.role_id = 1` (`$adminRole->id`).
   - Assigned Spatie role `syncRoles(['Admin'])` in `model_has_roles`.
2. **`Gate::before` Super Admin Bypass (`app/Providers/AppServiceProvider.php:26-28`):**
   ```php
   Gate::before(function ($user, $ability) {
       return $user->hasRole(['Admin', 'Super Admin']) ? true : null;
   });
   ```
   Bypasses all permission checks for users holding either `'Admin'` or `'Super Admin'` role.
3. **`BranchScope` Multi-Tenancy Bypass (`app/Models/Scopes/BranchScope.php:20`):**
   ```php
   if (!empty($user->branch_id) && $user->role_id !== 1 && !$user->hasRole(['Admin', 'Super Admin'])) {
       $builder->where($model->getTable() . '.branch_id', $user->branch_id);
   }
   ```
   Ensures users with `role_id === 1` OR holding Spatie roles `Admin`/`Super Admin` view statewide records unconstrained.

---

### 1.4 Blade Views & Client-side AJAX Scripts
1. **Blade Index (`resources/views/pages/authentication/roles/index.blade.php`):**
   - Line 26: `@can('roles.create')` wraps "Add" button targeting modal `#roleModal`.
   - Lines 50-52: Appends `<span class="badge badge-xs bg-danger ms-1">Super Admin</span>` if `role->name === 'Admin' || role->name === 'Super Admin'`.
   - Lines 55-66: Renders "All Permissions (Super Admin)" badge for Admin, else renders count and comma-separated preview of permissions.
   - Lines 69-76: `@can('roles.edit')` wraps edit button (`.editBtn`) carrying `data-id` and `data-name`.
   - Lines 78-86: `@can('roles.delete')` wraps delete button (`.deleteBtn`), conditionally hidden for Admin and Super Admin (`@if($role->name !== 'Admin' && $role->name !== 'Super Admin')`).
2. **Blade Modals (`resources/views/pages/authentication/roles/createrole.blade.php`):**
   - Modal 1 (`#roleModal`): Add role form `#rolesadd` with dynamic module cards and checkboxes `name="permissions[]"`.
   - Modal 2 (`#roleeditModal`): Edit role form `#rolesedit` with hidden input `#role_id` and name input `#role_name`.
   - Bulk toggles: "Select All", "Deselect All", and per-module switch "All".
3. **AJAX Client (`public/js/ajax/role.js`):**
   - Lines 41-68: Submits `POST roleadd`. On validation failure (HTTP 422), iterates `xhr.responseJSON.errors` and populates `.<key>_error`. On success, displays `toastr.success` and reloads after 800ms.
   - Lines 71-111: On `.editBtn` click, fires `GET roles/{id}/permissions`. On success, checks assigned checkboxes (`assignedPerms.includes($(this).val())`) and opens modal.
   - Lines 113-144: Submits `POST roleupdate`. Displays toastr and reloads.
   - Lines 146-192: On `.deleteBtn` click, prompts SweetAlert2 dialog (`Swal.fire`). On confirmation, sends `POST roledelete`. If `response.status === 1`, removes row from DataTable (`table.row(...).remove().draw()`). If `response.status === 0`, displays SweetAlert error dialog.

---

## 2. Logic Chain

1. **Route to Controller Mapping:**
   From `routes/web.php:126-134`, all 5 role operations (`index`, `getPermissions`, `store`, `update`, `destroy`) map directly to `RolesController`. All routes require authentication (`auth`) and are protected by granular Spatie permissions (`roles.view`, `roles.create`, `roles.edit`, `roles.delete`).
2. **Access Control Evaluation Chain:**
   When an HTTP request enters `/roles`:
   - `auth` middleware verifies session.
   - `permission:roles.view` invokes Laravel's authorization gate.
   - `AppServiceProvider:26-28` triggers `Gate::before`:
     - If user has role `'Admin'` or `'Super Admin'`, gate immediately returns `true`, granting access.
     - If user has other roles (e.g., `'Staff'`, `'Officer'`), gate returns `null`, prompting Spatie to check `model_has_roles` -> `role_has_permissions`.
     - Staff (has only `users.view`, `customer.view`, etc.) does not have `roles.view`, resulting in HTTP 403 Forbidden.
3. **Permission Grouping Logic:**
   In `RolesController::index`, exploding strings like `category.view` on `.` isolates `category`. The hardcoded `$modules` array translates this to `'Categories'`. Any unregistered key falls back to `ucfirst($moduleKey)` in `createrole.blade.php:32`.
4. **Data Integrity & Destruction Logic:**
   - In `RolesController::destroy`, checking `$role->name === 'Admin' || $role->name === 'Super Admin'` acts as a fail-safe against API-level attacks attempting to delete protected roles even if the UI delete button is hidden.
   - When custom roles are deleted, Spatie's foreign keys in `model_has_roles` and `role_has_permissions` cascade automatically.
5. **State Synchronization:**
   Every write operation (`store`, `update`, `destroy`) calls `PermissionRegistrar::forgetCachedPermissions()`, preventing stale permission cache from serving revoked access rights.

---

## 3. Caveats & Edge Cases

1. **Unhandled 500 on Invalid Permission Names:**
   In `store()` (`RolesController.php:47-50`) and `update()` (`RolesController.php:83-87`), validation specifies:
   `'permissions' => 'nullable|array'`
   It lacks array element validation: `'permissions.*' => 'string|exists:permissions,name'`.
   If a payload submits an unrecognized permission string (e.g. `permissions[] = 'invalid.foo'`), Spatie's `Role::syncPermissions` throws `Spatie\Permission\Exceptions\PermissionDoesNotExist`, causing an unhandled HTTP 500 error.
2. **Renaming Protected Roles in `update()`:**
   In `RolesController::update()`, there is no check preventing the `'Admin'` or `'Super Admin'` role from being renamed (e.g., changing role name to `'SubAdmin'`).
   If an administrator renames `'Admin'`, the `Gate::before` check, `BranchScope` bypass, and `RolesController::destroy` safeguard will fail to match `'Admin'`, causing system-wide permission escalation or lockout risks.
3. **Orphaned `users.role_id` References on Role Deletion:**
   In `2026_07_06_114649_add_role_id_and_branch_id_to_users_table.php:16`, `users.role_id` was added without database foreign key cascade constraints (`$table->foreignId('role_id')->nullable()`).
   When a custom role is deleted:
   - Spatie's `model_has_roles` entry is removed by foreign key cascade.
   - However, `users.role_id` still holds the deleted role's ID.
   - `RolesController::destroy` does not check if users are currently assigned to that role before deleting it.
   - While `users/index.blade.php:125` handles null relations gracefully (`$user->roles->first() ? ... : ($user->role ? ... : 'No Role')`), having dangling foreign IDs is a database hygiene caveat.
4. **Response Status vs HTTP Status Code:**
   In `RolesController::destroy()`, when deletion of `Admin` is rejected, it returns HTTP 200 with `{ "status": 0, "message": "..." }`, which the client-side JavaScript handles via SweetAlert (`public/js/ajax/role.js:175`). Test assertions must check `json('status') == 0` rather than expecting an HTTP 403 or 422.
5. **Missing Request Validation in `destroy()`:**
   `RolesController::destroy()` relies directly on `Role::findOrFail($request->id)` without preceding `$request->validate(['id' => 'required|exists:roles,id'])`. Missing or invalid IDs result in `ModelNotFoundException` (HTTP 404).

---

## 4. Conclusion

1. **Architecture Status:** The Roles and Permissions module is fully operational and adheres to Spatie RBAC standards. All 5 endpoints are cleanly mapped to `RolesController`, protected by granular permissions and augmented by an unconditional `Gate::before` super-admin bypass.
2. **Permission Model:** Exactly 48 permissions are seeded across 14 modules. Default roles possess calibrated privileges: `Admin` (48 perms / 100%), `Officer` (22 perms / 45.8%), `Staff` (11 perms / 22.9%).
3. **Security Safeguards:** `Admin` and `Super Admin` deletion is strictly blocked in both backend controller logic (`RolesController:112`) and frontend Blade rendering (`index.blade.php:79`).
4. **Testing Readiness:** The module is fully prepared for test suite implementation under R2. All routes, validation rules, AJAX contracts, and edge cases are completely mapped.

---

## 5. Blueprint for R2 Test Suite (`tests/Feature/RolesAndPermissionsTest.php`)

The test suite should be created at `tests/Feature/RolesAndPermissionsTest.php` utilizing `DatabaseTransactions` and the following structured test cases:

```php
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

        // Retrieve seeded users
        $this->adminUser = User::where('email', 'admin@gtms.com')->first() 
            ?: User::role('Admin')->first();
        $this->staffUser = User::where('email', 'test@example.com')->first() 
            ?: User::role('Staff')->first();
        $this->officerUser = User::where('email', 'officer@gtms.com')->first() 
            ?: User::role('Officer')->first();
    }

    /**
     * 1. Unauthenticated requests must redirect to login
     */
    public function test_guest_cannot_access_roles_endpoints(): void
    {
        $this->get(route('roles.index'))->assertRedirect(route('login'));
        $this->get(route('roles.permissions', 1))->assertRedirect(route('login'));
        $this->post(route('roleadd'), ['name' => 'Hacker'])->assertRedirect(route('login'));
        $this->post(route('roleupdate'), ['id' => 1, 'name' => 'Hacker'])->assertRedirect(route('login'));
        $this->post(route('roledelete'), ['id' => 1])->assertRedirect(route('login'));
    }

    /**
     * 2. Unauthorized staff user (without roles permissions) receives 403 Forbidden
     */
    public function test_unauthorized_user_is_forbidden(): void
    {
        $this->actingAs($this->staffUser);

        $this->get(route('roles.index'))->assertStatus(403);
        $this->get(route('roles.permissions', 1))->assertStatus(403);
        $this->post(route('roleadd'), ['name' => 'Staff Role'])->assertStatus(403);
        $this->post(route('roleupdate'), ['id' => 1, 'name' => 'Staff Edit'])->assertStatus(403);
        $this->post(route('roledelete'), ['id' => 1])->assertStatus(403);
    }

    /**
     * 3. Admin can view role directory and permission matrices
     */
    public function test_admin_can_view_roles_matrix_and_permission_counts(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('roles.index'));
        $response->assertStatus(200);
        $response->assertViewIs('pages.authentication.roles.index');
        $response->assertViewHasAll(['roles', 'groupedPermissions', 'modules']);
        $response->assertSee('Admin');
        $response->assertSee('Officer');
        $response->assertSee('Staff');
        $response->assertSee('All Permissions (Super Admin)');
    }

    /**
     * 4. Fetch assigned permissions for a role via AJAX
     */
    public function test_can_fetch_role_permissions_via_ajax(): void
    {
        $officerRole = Role::where('name', 'Officer')->first();

        $response = $this->actingAs($this->adminUser)->get(route('roles.permissions', $officerRole->id));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'role' => ['id', 'name', 'guard_name'],
            'permissions',
        ]);
        $response->assertJson([
            'status' => 1,
            'role' => ['name' => 'Officer'],
        ]);

        $perms = $response->json('permissions');
        $this->assertContains('customer.view', $perms);
        $this->assertContains('mining.view', $perms);
        $this->assertNotContains('roles.view', $perms);
    }

    /**
     * 5. 404 returned when fetching permissions for non-existent role
     */
    public function test_get_permissions_returns_404_for_invalid_id(): void
    {
        $this->actingAs($this->adminUser)
            ->get(route('roles.permissions', 999999))
            ->assertStatus(404);
    }

    /**
     * 6. Create new role and synchronize permissions
     */
    public function test_admin_can_create_new_role_with_permissions(): void
    {
        $payload = [
            'name' => 'Quarry Inspector ' . rand(1000, 9999),
            'permissions' => ['customer.view', 'mining.view', 'dgps.view'],
        ];

        $response = $this->actingAs($this->adminUser)->postJson(route('roleadd'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'message' => 'Role Added Successfully',
            'data' => [
                'name' => $payload['name'],
            ],
        ]);

        $this->assertDatabaseHas('roles', ['name' => $payload['name']]);
        $role = Role::where('name', $payload['name'])->first();
        $this->assertCount(3, $role->permissions);
    }

    /**
     * 7. Role creation enforces validation: required and unique name
     */
    public function test_role_creation_validation_errors(): void
    {
        // Missing name
        $this->actingAs($this->adminUser)
            ->postJson(route('roleadd'), ['name' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);

        // Duplicate name
        $this->actingAs($this->adminUser)
            ->postJson(route('roleadd'), ['name' => 'Admin'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * 8. Update role name and update permissions
     */
    public function test_admin_can_update_role_and_modify_permissions(): void
    {
        $role = Role::create(['name' => 'Temporary Role ' . rand(1000, 9999), 'guard_name' => 'web']);
        $role->syncPermissions(['customer.view', 'application.view']);

        $updatedName = 'Auditor ' . rand(1000, 9999);
        $payload = [
            'id' => $role->id,
            'name' => $updatedName,
            'permissions' => ['mining.view', 'drone.view'],
        ];

        $response = $this->actingAs($this->adminUser)->postJson(route('roleupdate'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'message' => 'Role Updated Successfully',
        ]);

        $role->refresh();
        $this->assertEquals($updatedName, $role->name);
        $this->assertEquals(['mining.view', 'drone.view'], $role->permissions->pluck('name')->sort()->values()->toArray());
    }

    /**
     * 9. Update role without permissions strips all permissions
     */
    public function test_update_role_without_permissions_revokes_all(): void
    {
        $role = Role::create(['name' => 'Clear Perms Role ' . rand(1000, 9999), 'guard_name' => 'web']);
        $role->syncPermissions(['customer.view']);

        $response = $this->actingAs($this->adminUser)->postJson(route('roleupdate'), [
            'id' => $role->id,
            'name' => $role->name,
            // permissions key omitted
        ]);

        $response->assertStatus(200);
        $this->assertCount(0, $role->fresh()->permissions);
    }

    /**
     * 10. Safeguard: Admin and Super Admin roles cannot be deleted
     */
    public function test_admin_and_super_admin_roles_cannot_be_deleted(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();

        $response = $this->actingAs($this->adminUser)->postJson(route('roledelete'), [
            'id' => $adminRole->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 0,
            'message' => 'Default Administrator role cannot be deleted.',
        ]);

        $this->assertDatabaseHas('roles', ['id' => $adminRole->id, 'name' => 'Admin']);

        // Check if Super Admin role exists or created
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $saResponse = $this->actingAs($this->adminUser)->postJson(route('roledelete'), [
            'id' => $superAdminRole->id,
        ]);
        $saResponse->assertStatus(200);
        $saResponse->assertJson([
            'status' => 0,
            'message' => 'Default Administrator role cannot be deleted.',
        ]);
        $this->assertDatabaseHas('roles', ['name' => 'Super Admin']);
    }

    /**
     * 11. Custom role deletion succeeds and cleans up role
     */
    public function test_custom_role_can_be_deleted(): void
    {
        $role = Role::create(['name' => 'Deletable Role ' . rand(1000, 9999), 'guard_name' => 'web']);
        $role->syncPermissions(['customer.view']);

        $response = $this->actingAs($this->adminUser)->postJson(route('roledelete'), [
            'id' => $role->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 1,
            'message' => 'Role Deleted Successfully',
        ]);

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}
```

---

## 6. Verification Method

To independently verify all findings in this report:

1. **Verify Routes:**
   Inspect `routes/web.php` lines 126-134.
2. **Verify Controller Methods & Safeguards:**
   Inspect `app/Http/Controllers/RolesController.php` lines 12-127.
3. **Verify All 48 Permissions and Default Roles:**
   Inspect `database/seeders/RolePermissionSeeder.php` lines 25-160.
4. **Verify Super Admin Gate Bypass:**
   Inspect `app/Providers/AppServiceProvider.php` lines 26-28.
5. **Verify Multi-Tenancy Scope Exemption:**
   Inspect `app/Models/Scopes/BranchScope.php` lines 17-23.
6. **Verify Views and AJAX Client:**
   Inspect `resources/views/pages/authentication/roles/index.blade.php`, `resources/views/pages/authentication/roles/createrole.blade.php`, and `public/js/ajax/role.js`.

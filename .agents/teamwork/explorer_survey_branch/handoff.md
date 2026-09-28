# Comprehensive Survey & Architectural Audit: Branch Management & Multi-Tenancy Scope (R1 & R4)

**Agent:** `explorer_survey_branch`  
**Date:** 2026-09-28  
**Parent:** `orchestrator_2` (Conversation ID: `6b69e301-99cc-4206-b8c6-8af6297273f3`)  
**Status:** Complete  

---

## 1. Observation

### 1.1 Complete Catalog of Branch Routes
In `routes/web.php` (lines 34–332 wrapped inside `Route::middleware('auth')->group(...)`), the branch routes are defined at lines 143–150:

```php
// File: routes/web.php (Lines 143-150)
// Branch / Department routes
Route::middleware('permission:branch.view')->group(function () {
    Route::get('/branch', [BranchController::class, 'index'])->name('branch.index');
});
Route::post('branchadd', [BranchController::class, 'store'])->name('branchadd')->middleware('permission:branch.create');
Route::post('branchedit', [BranchController::class, 'update'])->name('branchedit')->middleware('permission:branch.edit');
Route::post('branchdelete', [BranchController::class, 'destroy'])->name('branchdelete')->middleware('permission:branch.delete');
```

| # | HTTP Verb | URI | Route Name | Action Controller & Method | Full Middleware Stack | Permission Guard | Purpose |
|---|-----------|-----|------------|----------------------------|------------------------|------------------|---------|
| 1 | `GET` | `/branch` | `branch.index` | `App\Http\Controllers\BranchController@index` | `web`, `auth`, `permission:branch.view` | `branch.view` | Department / Branch master directory view |
| 2 | `POST` | `/branchadd` | `branchadd` | `App\Http\Controllers\BranchController@store` | `web`, `auth`, `permission:branch.create` | `branch.create` | Add new branch (AJAX modal submit) |
| 3 | `POST` | `/branchedit` | `branchedit` | `App\Http\Controllers\BranchController@update` | `web`, `auth`, `permission:branch.edit` | `branch.edit` | Update branch metadata & status (AJAX modal submit) |
| 4 | `POST` | `/branchdelete` | `branchdelete` | `App\Http\Controllers\BranchController@destroy` | `web`, `auth`, `permission:branch.delete` | `branch.delete` | Delete branch record (AJAX SweetAlert confirm) |

*Super Admin Bypass Observation:*  
In `app/Providers/AppServiceProvider.php` (lines 26–28):
```php
Gate::before(function ($user, $ability) {
    return $user->hasRole(['Admin', 'Super Admin']) ? true : null;
});
```
Any user with the Spatie role `'Admin'` or `'Super Admin'` bypasses all `permission:*` middleware checks automatically.

---

### 1.2 Detailed Audit of `BranchController`
Source file: `app/Http/Controllers/BranchController.php` (82 lines).

#### Method 1: `index()` (Lines 10–14)
```php
public function index(){

    $branches = Branch::get();
    return view('pages.authentication.branch.index', compact('branches'));
}
```
- **Inputs**: None.
- **Database Query**: `Branch::get()`. Fetches all records from `branches` table unconditionally.
- **Active/Inactive Filtering**:
  - No server-side query filtering exists in `BranchController@index`.
  - Both active (`status = 1`) and inactive (`status = 0`) branches are loaded into memory and passed to the view.
  - Client-side filtering/searching is performed by DataTables (`#example10`).
  - *Contrast with `UserController@index` (line 17)*: In `UserController`, only active branches are loaded for user assignment: `$branch = Branch::where('status', 1)->get();`.
- **View Target**: `pages.authentication.branch.index` (`resources/views/pages/authentication/branch/index.blade.php`).
- **Response**: HTTP 200 HTML response.
- **Flash Messages / Redirects**: None.

#### Method 2: `store(Request $request)` (Lines 16–41)
```php
public function store(Request $request){

    $request->validate([
        'branch_name' => 'required',
        'contact_person' => 'required',
        'mobile' => 'required',
        'address' => 'required',
    ]);

    $branch = new Branch();
    $branch->branch_name = $request->branch_name;
    $branch->contact_person = $request->contact_person;
    $branch->mobile = $request->mobile;
    $branch->address = $request->address;
    $branch->city = $request->city;
    $branch->state = $request->state;
    $branch->pincode = $request->pincode;
    $branch->status = 1;
    $branch->save();

    return response()->json([
        'status'=>1,
        'message'=>'Branch Added Successfully',
        'data'=>$branch
    ]);
}
```
- **Inputs**: `branch_name` (required), `contact_person` (required), `mobile` (required), `address` (required), `city` (optional), `state` (optional), `pincode` (optional).
- **Validation Rules**:
  - `branch_name` => `'required'`
  - `contact_person` => `'required'`
  - `mobile` => `'required'`
  - `address` => `'required'`
  - *Note*: `city`, `state`, `pincode` are unvalidated in `$request->validate()` but assigned directly if present.
- **Status Assignment**: Hardcoded to `1` (`$branch->status = 1;`).
- **Response**: HTTP 200 JSON:
  ```json
  {
      "status": 1,
      "message": "Branch Added Successfully",
      "data": { ... }
  }
  ```
- **Validation Failure Behavior**:
  - When invoked via AJAX (`Accept: application/json` or `X-Requested-With: XMLHttpRequest`), standard Laravel `$request->validate()` throws `ValidationException`, returning HTTP 422 JSON (`{"message": "...", "errors": {...}}`).
  - *Frontend discrepancy note*: `public/js/ajax/branch.js` lines 18–24 checks `if (response.status == 0) { ... }` inside jQuery `success` callback, whereas Laravel returns HTTP 422 which triggers jQuery's `error` callback.
- **Potential 500 Edge Cases**:
  - Length overflow: Migration `branches` defines `$table->string('mobile', 15)` and `$table->string('pincode', 10)`. Validation lacks string length limits (`'mobile' => 'max:15'`, `'pincode' => 'max:10'`). Sending a mobile string >15 chars triggers `QueryException: Data too long for column 'mobile'` on MySQL in strict mode.

#### Method 3: `update(Request $request)` (Lines 43–68)
```php
public function update(Request $request){

    $request->validate([
        'branch_name' => 'required',
        'contact_person' => 'required',
        'mobile' => 'required',
        'address' => 'required',
    ]);

    $branch = Branch::find($request->id);
    $branch->branch_name = $request->branch_name;
    $branch->contact_person = $request->contact_person;
    $branch->mobile = $request->mobile;
    $branch->address = $request->address;
    $branch->city = $request->city;
    $branch->state = $request->state;
    $branch->pincode = $request->pincode;
    $branch->status = $request->status;
    $branch->save();

    return response()->json([
        'status'=>1,
        'message'=>'Branch Updated Successfully',
        'data'=>$branch
    ]);
}
```
- **Inputs**: `id` (branch ID), `branch_name` (required), `contact_person` (required), `mobile` (required), `address` (required), `city` (optional), `state` (optional), `pincode` (optional), `status` (0 or 1).
- **Validation Rules**: Same as `store()`. Critically, `id` is NOT in the validation rules!
- **Response**: HTTP 200 JSON:
  ```json
  {
      "status": 1,
      "message": "Branch Updated Successfully",
      "data": { ... }
  }
  ```
- **Status Toggle**: Handled here via `$branch->status = $request->status;`. The frontend modal (`creatbranch.blade.php` lines 109–114) provides a dropdown with `<option value="1">Active</option>` and `<option value="0">Inactive</option>`.
- **CRITICAL UNHANDLED 500 EDGE CASE**:
  - Missing or non-existent `id`: Because `id` is not validated (`'id' => 'required|exists:branches,id'`), calling `Branch::find($request->id)` with `null` or a non-existent ID (e.g. `999999`) returns `null`. Line 53 immediately crashes: `Error: Attempt to assign property "branch_name" on null`, throwing an unhandled HTTP 500 exception!

#### Method 4: `destroy(Request $request)` (Lines 70–80)
```php
public function destroy(Request $request){

    $branch = Branch::find($request->id);
    $branch->delete();

    return response()->json([
        'status'=>1,
        'message'=>'Branch Deleted Successfully',
        'data'=>$branch
    ]);
}
```
- **Inputs**: `id` in POST request payload.
- **Validation**: None.
- **Response**: HTTP 200 JSON:
  ```json
  {
      "status": 1,
      "message": "Branch Deleted Successfully",
      "data": { ... }
  }
  ```
- **CRITICAL UNHANDLED 500 EDGE CASES**:
  1. Missing or non-existent `id`: If `id` does not match an existing record or is absent, `$branch` is `null`. Line 73 executes `null->delete()`, throwing `Error: Call to a member function delete() on null`, resulting in an unhandled HTTP 500.
  2. Foreign Key Integrity Constraints:
     - The `branches` table uses hard deletes (no `SoftDeletes` trait on `Branch.php`).
     - If the branch is assigned to a user in `users.branch_id` and the database enforces an un-cascaded foreign key, MySQL throws `QueryException: Integrity constraint violation (SQLSTATE[23000])` -> HTTP 500.
     - Note: In workflow tables (`lease_applications`, `mining_applications`, `ppt_applications`, `dgps_surveys`, `drone_surveys`, `ec_compliances`, `mineral_stockpiles`), the foreign key is defined with `nullOnDelete()`, and on `products` it is `cascadeOnDelete()`.

---

### 1.3 Branch Model & Database Schema

#### Model: `app/Models/Branch.php` (Lines 1–12)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
     protected $table = 'branches';
     protected $guarded = [];
}
```
- **Table**: `branches`
- **Guarded**: `[]` (all columns mass-assignable)
- **Soft Deletes**: Not implemented (hard deletes are performed).
- **Relationships Defined on Branch Model**: None explicitly defined inside `Branch.php`. Reverse relations (`belongsTo(Branch::class)`) are defined on dependent models (`User`, `Product`, and models using `BelongsToBranch`).

#### Database Migration: `database/migrations/2026_07_07_093905_create_branches_table.php`
```php
Schema::create('branches', function (Blueprint $table) {
    $table->id();
    $table->string('branch_name');
    $table->string('contact_person')->nullable();
    $table->string('mobile', 15)->nullable();
    $table->text('address')->nullable();
    $table->string('city')->nullable();
    $table->string('state')->nullable();
    $table->string('country')->default('India');
    $table->string('pincode', 10)->nullable();
    $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
    $table->timestamps();
});
```
- **Active / Inactive Flag**: Column `status` (tinyInteger).
  - Value `1`: Active (default)
  - Value `0`: Inactive

---

### 1.4 Multi-Tenancy Architecture: `BranchScope` & `BelongsToBranch`

#### 1.4.1 Scope Implementation: `app/Models/Scopes/BranchScope.php` (Lines 10–25)
```php
namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (Auth::hasUser()) {
            $user = Auth::user();
            // If user has a specific branch assigned and is not a superadmin (role_id !== 1 and not Admin/Super Admin role)
            if (!empty($user->branch_id) && $user->role_id !== 1 && !$user->hasRole(['Admin', 'Super Admin'])) {
                $builder->where($model->getTable() . '.branch_id', $user->branch_id);
            }
        }
    }
}
```

#### 1.4.2 Trait Implementation: `app/Models/Traits/BelongsToBranch.php` (Lines 10–36)
```php
namespace App\Models\Traits;

use App\Models\Branch;
use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToBranch
{
    /**
     * Boot the BelongsToBranch trait.
     */
    protected static function bootBelongsToBranch(): void
    {
        static::addGlobalScope(new BranchScope());

        static::creating(function ($model) {
            if (Auth::hasUser() && empty($model->branch_id)) {
                $user = Auth::user();
                if (!empty($user->branch_id)) {
                    $model->branch_id = $user->branch_id;
                }
            }
        });
    }

    /**
     * Relationship to Branch.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
```

#### 1.4.3 Architectural Rules of `BranchScope`:
1. **Authenticated Context Check**:
   - `Auth::hasUser()` must be true. Unauthenticated contexts (CLI, scheduled commands, guests) are not scoped.
2. **Dual-Gate Admin Bypass**:
   - Condition to apply filter:
     `!empty($user->branch_id) && $user->role_id !== 1 && !$user->hasRole(['Admin', 'Super Admin'])`
   - Bypass occurs if ANY of the following is true:
     - User has `role_id === 1` (Legacy Admin/Super Admin column)
     - User has Spatie role `'Admin'` or `'Super Admin'` via `hasRole()`
     - User has `branch_id === null` (Unassigned user statewide view fallback)
3. **Table Qualification**:
   - `$builder->where($model->getTable() . '.branch_id', $user->branch_id);`
   - Explicitly prefixes table name, preventing SQL ambiguity when joins are executed.
4. **Automatic Assignment on Model Creation**:
   - In `BelongsToBranch::bootBelongsToBranch()`, the Eloquent `creating` event checks if `$model->branch_id` is empty. If so, it automatically populates `$model->branch_id = $user->branch_id`.

#### 1.4.4 Complete List of Models Using `BranchScope` (via `BelongsToBranch` trait):
1. `App\Models\LeaseApplication` (`app/Models/LeaseApplication.php:16`)
2. `App\Models\MiningApplication` (`app/Models/MiningApplication.php:15`)
3. `App\Models\EnvironmentProject` (`app/Models/EnvironmentProject.php:14`)
4. `App\Models\PptApplication` (`app/Models/PptApplication.php:14`)
5. `App\Models\DgpsSurvey` (`app/Models/DgpsSurvey.php:14`)
6. `App\Models\DroneSurvey` (`app/Models/DroneSurvey.php:14`)
7. `App\Models\EcCompliance` (`app/Models/EcCompliance.php:14`)
8. `App\Models\MineralStockpile` (`app/Models/MineralStockpile.php:13`)

*(Note: `Branch`, `User`, `Customer`, `Product`, `Category`, `Unit` do NOT use `BranchScope`).*

---

## 2. Logic Chain

1. **Route to Controller Binding**:
   - Inspection of `routes/web.php:143-150` confirms all 4 endpoints (`/branch`, `branchadd`, `branchedit`, `branchdelete`) map directly to `BranchController`.
   - Each route is protected by `auth` and individual Spatie permissions: `branch.view`, `branch.create`, `branch.edit`, `branch.delete`.
   - `AppServiceProvider:26-28` provides global bypass for `'Admin'` and `'Super Admin'`.

2. **Controller Contract & Response Types**:
   - `index()` returns Blade view `pages.authentication.branch.index`.
   - `store()`, `update()`, `destroy()` all return JSON responses containing `status: 1`, `message: string`, and `data: Branch`.
   - Validation failure on `store()` and `update()` throws standard Laravel `ValidationException` (HTTP 422 for AJAX).

3. **Unhandled 500 Vulnerability Analysis**:
   - In `update()`, `Branch::find($request->id)` is used without validating `id`. If `id` is null or invalid, mutating `$branch->branch_name` raises `Attempt to assign property on null` (500).
   - In `destroy()`, `Branch::find($request->id)` is used without validating `id`. If null, `$branch->delete()` raises `Call to a member function delete() on null` (500).
   - Test suites must explicitly test valid IDs and record the behavior of invalid IDs.

4. **Multi-Tenancy Isolation Verification Logic**:
   - If User A has `branch_id = 1` and role `'Staff'` / `role_id = 2`:
     - Querying `LeaseApplication::all()` generates `WHERE lease_applications.branch_id = 1`.
     - Records belonging to `branch_id = 2` are omitted from queries and `find()` returns `null`.
   - If User B has `role_id = 1` or Spatie role `'Admin'`:
     - Global scope filter is suppressed.
     - Querying `LeaseApplication::all()` returns records from both `branch_id = 1` and `branch_id = 2`.
   - If User A creates a `LeaseApplication` without passing `branch_id`, `BelongsToBranch::bootBelongsToBranch()` automatically writes `branch_id = 1`.

---

## 3. Caveats

1. **Client-Side vs Server-Side Branch Filtering**:
   - `BranchController@index()` does not accept query parameters for active/inactive filtering (`?status=1`). It relies on DataTables on the client side.
   - However, in `UserController@index()`, server-side filtering `Branch::where('status', 1)->get()` is actively applied to populate the department dropdown. Test cases should verify both aspects.
2. **Missing Input Validation on IDs**:
   - Because `BranchController::update` and `BranchController::destroy` do not validate `id`, tests asserting "graceful validation error" on missing ID will instead observe an HTTP 500 `Error` exception unless guarded. This is documented as an unhandled edge case in the codebase.
3. **Database Transactions**:
   - All tests must use `DatabaseTransactions` to ensure test data does not contaminate the production/development database (`gtms_data`).

---

## 4. Conclusion

- The Branch module consists of 4 distinct routes with Spatie permission gating, handled exclusively via JSON for mutations (`store`, `update`, `destroy`) and Blade HTML for directory view (`index`).
- Multi-tenancy is implemented through `BelongsToBranch` trait and `BranchScope`, covering 8 workflow and survey models.
- Dual-role checks (`$user->role_id !== 1 && !$user->hasRole(['Admin', 'Super Admin'])`) ensure seamless administrative oversight while strictly partitioning branch staff.
- A dedicated feature test suite `tests/Feature/BranchManagementTest.php` and `tests/Feature/MultiTenancyBranchScopeTest.php` will satisfy R1 and R4 completely with zero application code changes required.

---

## 5. Blueprint for Test Cases (R1 & R4)

### 5.1 Test Suite 1: `BranchManagementTest.php` (Requirement R1)
Location: `tests/Feature/BranchManagementTest.php`  
Traits: `DatabaseTransactions`  
Database config: `mysql` (`gtms_data`)

#### Group A: Authentication & Authorization Gating
1. `test_guest_cannot_access_branch_routes()`:
   - Unauthenticated GET `/branch` -> redirects to login (302).
   - Unauthenticated POST `/branchadd` -> redirects to login (302).
   - Unauthenticated POST `/branchedit` -> redirects to login (302).
   - Unauthenticated POST `/branchdelete` -> redirects to login (302).
2. `test_user_without_branch_view_permission_is_forbidden()`:
   - User without `branch.view` permission (e.g. `Staff` role) accessing `GET /branch` receives HTTP 403 Forbidden.
3. `test_user_without_branch_create_permission_cannot_create_branch()`:
   - User without `branch.create` permission POST `/branchadd` receives HTTP 403 Forbidden.
4. `test_user_without_branch_edit_permission_cannot_update_branch()`:
   - User without `branch.edit` permission POST `/branchedit` receives HTTP 403 Forbidden.
5. `test_user_without_branch_delete_permission_cannot_delete_branch()`:
   - User without `branch.delete` permission POST `/branchdelete` receives HTTP 403 Forbidden.

#### Group B: Directory Viewing & Active/Inactive State
6. `test_authorized_user_can_view_branch_index()`:
   - User with `Admin` role GET `/branch` receives HTTP 200, view has `branches` data, contains table `#example10`.
7. `test_branch_index_renders_both_active_and_inactive_branches()`:
   - Create active branch (`status = 1`) and inactive branch (`status = 0`).
   - GET `/branch` response sees active badge `<span class="badge badge-success">Active</span>` and inactive badge `<span class="badge badge-danger">Inactive</span>`.
8. `test_user_management_filters_inactive_branches_from_dropdown()`:
   - GET `/user` receives HTTP 200; only active branches (`status = 1`) are present in `$branch` collection passed to view.

#### Group C: Branch Creation (`store`)
9. `test_create_branch_validation_errors()`:
   - POST `/branchadd` with empty payload receives HTTP 422 JSON with validation errors for `branch_name`, `contact_person`, `mobile`, `address`.
10. `test_create_branch_successful_persistence()`:
    - POST `/branchadd` with valid payload (`branch_name`, `contact_person`, `mobile`, `address`, `city`, `state`, `pincode`).
    - Receives HTTP 200 JSON `{status: 1, message: "Branch Added Successfully"}`.
    - Database has record in `branches` table with `status = 1` and `country = 'India'`.

#### Group D: Branch Updating & Status Toggling (`update`)
11. `test_update_branch_validation_errors()`:
    - POST `/branchedit` with empty payload receives HTTP 422 JSON.
12. `test_update_branch_metadata_successfully()`:
    - Create a test branch.
    - POST `/branchedit` with updated `branch_name`, `contact_person`, `mobile`, `address`, `city`, `state`, `pincode`.
    - Receives HTTP 200 JSON `{status: 1, message: "Branch Updated Successfully"}`.
    - Database reflects updated metadata.
13. `test_toggle_branch_status_to_inactive()`:
    - POST `/branchedit` with `status = 0`.
    - Database has `status = 0` for that branch.
14. `test_toggle_branch_status_to_active()`:
    - POST `/branchedit` with `status = 1`.
    - Database has `status = 1` for that branch.

#### Group E: Branch Deletion & Integrity (`destroy`)
15. `test_delete_branch_successfully()`:
    - Create a branch with no foreign key references.
    - POST `/branchdelete` with `id`.
    - Receives HTTP 200 JSON `{status: 1, message: "Branch Deleted Successfully"}`.
    - Database verifies record missing from `branches`.
16. `test_delete_branch_nullifies_child_application_branch_id()`:
    - Create branch, associate a `LeaseApplication` with `branch_id`.
    - Delete branch.
    - Database verifies `lease_applications.branch_id` is set to `NULL` (via MySQL `nullOnDelete` constraint).

---

### 5.2 Test Suite 2: `MultiTenancyBranchScopeTest.php` (Requirement R4)
Location: `tests/Feature/MultiTenancyBranchScopeTest.php`  
Traits: `DatabaseTransactions`  
Database config: `mysql` (`gtms_data`)

#### Group A: Non-Admin User Scope Isolation
1. `test_non_admin_user_restricted_to_own_branch_records()`:
   - Create Branch 1 and Branch 2.
   - Create Non-Admin User assigned to Branch 1 (`role_id = 2`, Spatie role `'Officer'`, `branch_id = Branch 1`).
   - Create `LeaseApplication` A in Branch 1 and `LeaseApplication` B in Branch 2.
   - Act as Non-Admin User:
     - `LeaseApplication::all()` returns only Record A.
     - Record B is NOT present.
2. `test_non_admin_cannot_query_or_find_other_branch_record()`:
   - Act as Non-Admin User (Branch 1):
     - `LeaseApplication::find(Record B->id)` returns `null`.
     - `LeaseApplication::where('id', Record B->id)->exists()` returns `false`.

#### Group B: Super Admin & Admin Unrestricted Statewide Bypass
3. `test_super_admin_bypasses_branch_scope_via_role_id()`:
   - Create Admin User with `role_id = 1`, `branch_id = Branch 1`.
   - Act as Admin User:
     - `LeaseApplication::all()` returns both Record A (Branch 1) and Record B (Branch 2).
4. `test_admin_bypasses_branch_scope_via_spatie_role()`:
   - Create User with `role_id = 99` (non-1), `branch_id = Branch 1`, but assigned Spatie role `'Admin'`.
   - Act as User:
     - `LeaseApplication::all()` returns both Record A and Record B.
5. `test_super_admin_spatie_role_bypasses_branch_scope()`:
   - Create User with Spatie role `'Super Admin'`.
   - Act as User:
     - `LeaseApplication::all()` returns all records statewide.

#### Group C: Edge Cases & Unassigned Users
6. `test_user_with_null_branch_id_is_unscoped()`:
   - Create User with `branch_id = null`, `role_id = 2`, Spatie role `'Staff'`.
   - Act as User:
     - Querying `LeaseApplication::all()` is not constrained by `BranchScope` (returns all records).
7. `test_unauthenticated_context_is_unscoped()`:
   - Log out / ensure no authenticated user:
     - `LeaseApplication::all()` executes without `BranchScope` constraint.

#### Group D: Automatic Branch Assignment (`BelongsToBranch::creating`)
8. `test_model_creation_auto_assigns_authenticated_user_branch_id()`:
   - Act as Non-Admin User with `branch_id = Branch 1`.
   - Create new `LeaseApplication` without passing `branch_id`.
   - Refresh model: verify `$lease->branch_id === Branch 1->id`.
9. `test_explicit_branch_id_is_preserved_on_model_creation()`:
   - Act as Non-Admin User with `branch_id = Branch 1`.
   - Create new `LeaseApplication` explicitly passing `branch_id = Branch 2->id`.
   - Refresh model: verify `$lease->branch_id === Branch 2->id`.

#### Group E: Direct Scope Bypass
10. `test_without_global_scope_bypasses_branch_scope()`:
    - Act as Non-Admin User with `branch_id = Branch 1`.
    - `LeaseApplication::withoutGlobalScope(BranchScope::class)->get()` returns all records including Branch 2.

#### Group F: Cross-Model Consistency Across All 8 Scoped Models
11. `test_all_eight_models_implement_belongs_to_branch_trait()`:
    - Verify `BelongsToBranch` trait is loaded and `BranchScope` global scope is registered on:
      1. `LeaseApplication`
      2. `MiningApplication`
      3. `EnvironmentProject`
      4. `PptApplication`
      5. `DgpsSurvey`
      6. `DroneSurvey`
      7. `EcCompliance`
      8. `MineralStockpile`

---

## 6. Verification Method

To independently verify all findings and test suite blueprints:
1. **Inspect Route Catalog**:
   - Review `routes/web.php` lines 143–150 and `app/Providers/AppServiceProvider.php` lines 26–28.
2. **Inspect Controller Logic**:
   - Review `app/Http/Controllers/BranchController.php` lines 10–80.
3. **Inspect Multi-Tenancy Architecture**:
   - Review `app/Models/Scopes/BranchScope.php` lines 10–25.
   - Review `app/Models/Traits/BelongsToBranch.php` lines 10–36.
4. **Inspect Database Migrations**:
   - Review `database/migrations/2026_07_07_093905_create_branches_table.php`.
   - Review foreign key references in `database/migrations/2026_09_04_000003_create_lease_module_tables.php:38`.
5. **Execution Verification (Once Implemented)**:
   - Run `php artisan test --filter=BranchManagementTest`
   - Run `php artisan test --filter=MultiTenancyBranchScopeTest`
   - Run `php artisan test` to verify zero regressions across existing test suites (`ApplicationHandlersAndPaymentsTest`, `PptDgpsAndEcComplianceTest`).

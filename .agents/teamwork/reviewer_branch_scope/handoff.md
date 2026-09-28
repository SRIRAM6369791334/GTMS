# Handoff Report: Review & Adversarial Audit of Branch Management & Multi-Tenancy Scope (R1 & R4)

**Agent:** `reviewer_branch_scope`  
**Date:** 2026-09-28  
**Parent:** `orchestrator_2` (Conversation ID: `6b69e301-99cc-4206-b8c6-8af6297273f3`)  
**Verdict:** **APPROVE**  
**Integrity Status:** **VERIFIED (Zero Integrity Violations)**

---

## 1. Observation

### 1.1 Source Code & Controller Hardening
In `app/Http/Controllers/BranchController.php`:
- `update(Request $request)` (lines 43–71):
  ```php
  $request->validate([
      'id' => 'required|exists:branches,id',
      'branch_name' => 'required',
      'contact_person' => 'required',
      'mobile' => 'required',
      'address' => 'required',
  ]);
  ```
- `destroy(Request $request)` (lines 73–87):
  ```php
  $request->validate([
      'id' => 'required|exists:branches,id',
  ]);
  ```
  Both methods strictly validate the incoming `id` parameter against the `branches` table before attempting `Branch::find($request->id)` or invoking any model methods.

### 1.2 Route Middleware & Permission Gating
In `routes/web.php` (lines 144–150):
```php
Route::middleware('permission:branch.view')->group(function () {
    Route::get('/branch', [BranchController::class, 'index'])->name('branch.index');
});
Route::post('branchadd', [BranchController::class, 'store'])->name('branchadd')->middleware('permission:branch.create');
Route::post('branchedit', [BranchController::class, 'update'])->name('branchedit')->middleware('permission:branch.edit');
Route::post('branchdelete', [BranchController::class, 'destroy'])->name('branchdelete')->middleware('permission:branch.delete');
```
All four routes are nested within `Route::middleware('auth')` (line 34). In `app/Providers/AppServiceProvider.php` (lines 26–28):
```php
Gate::before(function ($user, $ability) {
    return $user->hasRole(['Admin', 'Super Admin']) ? true : null;
});
```
This guarantees unauthorized guests receive 302 redirects, unauthorized users receive 403 Forbidden responses, and authorized administrators or users with individual permissions receive access.

### 1.3 Multi-Tenancy Architecture
1. In `app/Models/Scopes/BranchScope.php` (lines 10–25):
   ```php
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
   ```
   Columns are qualified with `$model->getTable() . '.branch_id'` to avoid ambiguous column conflicts during SQL joins.
2. In `app/Models/Traits/BelongsToBranch.php` (lines 10–36):
   - Registers `BranchScope` via `static::addGlobalScope(new BranchScope())`.
   - In `creating` lifecycle hook, automatically populates `$model->branch_id = $user->branch_id` if empty and user has `branch_id`.
   - Preserves explicitly supplied `branch_id`.
   - Defines standard `branch(): BelongsTo` relationship to `App\Models\Branch`.
3. In `database/migrations/`:
   All 8 multi-tenant tables (`lease_applications`, `mining_applications`, `environment_projects`, `ppt_applications`, `dgps_surveys`, `drone_surveys`, `mineral_stockpiles`, `ec_compliances`) define:
   `$table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();`

### 1.4 Empirical Test Execution Results
All test commands were executed directly on the host environment:

1. **Target Feature Test Suites**:
   ```powershell
   php artisan test tests/Feature/BranchManagementTest.php tests/Feature/MultiTenancyBranchScopeTest.php
   ```
   **Output:**
   ```
      PASS  Tests\Feature\BranchManagementTest
     ✓ guest redirected from all branch endpoints                                                                   0.74s  
     ✓ unauthorized user forbidden on branch view                                                                   0.23s  
     ✓ unauthorized user forbidden on branch create                                                                 0.17s  
     ✓ unauthorized user forbidden on branch edit                                                                   0.16s  
     ✓ unauthorized user forbidden on branch delete                                                                 0.18s  
     ✓ authorized user can view branch index                                                                        0.31s  
     ✓ branch index renders both active and inactive branches                                                       0.36s  
     ✓ user management filters inactive branches for department assignment                                          1.64s  
     ✓ branch creation validation errors                                                                            0.18s  
     ✓ branch creation successful persistence                                                                       0.17s  
     ✓ branch update validation errors                                                                              0.16s  
     ✓ branch update metadata successfully                                                                          0.14s  
     ✓ toggle branch status to inactive and active                                                                  0.19s  
     ✓ delete branch successfully                                                                                   0.16s  
     ✓ delete branch nullifies child application branch id                                                          0.30s  
     ✓ zero unhandled 500 on update with missing or invalid id                                                      0.16s  
     ✓ zero unhandled 500 on destroy with missing or invalid id                                                     0.16s  

      PASS  Tests\Feature\MultiTenancyBranchScopeTest
     ✓ non admin user restricted to own branch records                                                              0.16s  
     ✓ non admin cannot query or find other branch record                                                           0.13s  
     ✓ super admin bypasses branch scope via role id one                                                            0.16s  
     ✓ admin bypasses branch scope via spatie admin role                                                            0.15s  
     ✓ super admin spatie role bypasses branch scope                                                                0.13s  
     ✓ user with null branch id is unscoped                                                                         0.15s  
     ✓ unauthenticated context is unscoped                                                                          0.15s  
     ✓ model creation auto assigns authenticated non admin user branch id when empty                                0.14s  
     ✓ explicit branch id is preserved on model creation                                                            0.12s  
     ✓ without global scope bypasses branch scope                                                                   0.17s  
     ✓ all eight models implement belongs to branch and register branch scope                                       0.12s  
     ✓ cross model branch isolation on mineral stockpile                                                            0.17s  

     Tests:    29 passed (354 assertions)
     Duration: 7.51s
   ```

2. **Regression Test Suite 1**:
   ```powershell
   php artisan test --filter=ApplicationHandlersAndPaymentsTest
   ```
   **Output:**
   ```
      PASS  Tests\Feature\ApplicationHandlersAndPaymentsTest
     ✓ schema integrity and model relations                                                                         0.79s  
     ✓ lease application step6 handlers workflow                                                                    0.35s  
     ✓ lease application step7 payment workflow and calculation                                                     0.13s  
     ✓ lease application step8 review displays handlers and payment                                                 0.10s  
     ✓ mining application intake saves handlers and payment                                                         0.56s  
     ✓ payment ledger status derivation logic                                                                       0.07s  

     Tests:    6 passed (68 assertions)
     Duration: 2.40s
   ```

3. **Regression Test Suite 2**:
   ```powershell
   php artisan test --filter=PptDgpsAndEcComplianceTest
   ```
   **Output:**
   ```
      PASS  Tests\Feature\PptDgpsAndEcComplianceTest
     ✓ ppt department index and dossier                                                                             0.73s  
     ✓ ppt department wizard steps                                                                                  0.83s  
     ✓ dgps survey index and dossier                                                                                0.29s  
     ✓ dgps survey wizard steps                                                                                     1.00s  
     ✓ ec compliance index and dossier                                                                              0.78s  
     ✓ ec compliance wizard steps                                                                                   2.62s  

     Tests:    6 passed (82 assertions)
     Duration: 6.69s
   ```

---

## 2. Logic Chain

1. **R1 Conformance (Branch Management Lifecycle)**:
   - **Directory & Filtering**: As shown in observation 1.4, `BranchManagementTest` verifies that the directory renders active and inactive badges (`badge-success` and `badge-danger`), while `user.index` dropdown strictly filters out inactive branches (`status = 0`), ensuring users cannot be provisioned into dormant departments.
   - **Creation**: Validates required fields (`branch_name`, `contact_person`, `mobile`, `address`) returning 422, and persists full metadata with default `status = 1` and `country = India`.
   - **Update & Status Toggle**: Validates required ID and fields, persists metadata updates, and toggles status between 0 and 1 with immediate database assertion.
   - **Deletion & DB Integrity**: Deleting a branch removes it from `branches` and triggers MySQL's `nullOnDelete` foreign key rule, setting `lease_applications.branch_id` to `NULL` without throwing integrity violations.

2. **R4 Conformance (Multi-Tenancy Branch Scope)**:
   - **Scope Enforcement**: As shown in observation 1.3 and 1.4, non-admin users with an assigned `branch_id` are strictly isolated to their own branch. Read queries (`all()`, `get()`, `find()`, `where()->exists()`) append `WHERE <table>.branch_id = ?`, returning `null` or `false` when querying out-of-scope branch data.
   - **Dual Admin Bypass**: Verified both legacy `role_id === 1` and Spatie roles (`Admin`, `Super Admin`) bypass `BranchScope`, returning statewide records.
   - **Edge Cases**: Users with `branch_id = null` and unauthenticated contexts (CLI/queue jobs) evaluate as unscoped, preventing background workers from crashing.
   - **Auto-Assignment & Explicit Preservation**: Verified `BelongsToBranch::creating` hook auto-assigns the authenticated user's `branch_id` if blank, and preserves explicit values if provided.
   - **All 8 Models Verified**: All 8 domain models (`LeaseApplication`, `MiningApplication`, `EnvironmentProject`, `PptApplication`, `DgpsSurvey`, `DroneSurvey`, `EcCompliance`, `MineralStockpile`) possess the trait, register `BranchScope`, and establish the `belongsTo(Branch::class)` relationship.

3. **Zero Unhandled 500 Exceptions**:
   - The root cause of previous 500 errors was unvalidated `id` parameters in `BranchController::update` and `BranchController::destroy`.
   - By adding `'id' => 'required|exists:branches,id'`, invalid or omitted IDs trigger Laravel's `ValidationException`, producing HTTP 422 JSON responses. This was empirically tested in `test_zero_unhandled_500_on_update_with_missing_or_invalid_id` and `test_zero_unhandled_500_on_destroy_with_missing_or_invalid_id`.

4. **Integrity & Authenticity**:
   - Tests do not mock or stub business logic; they execute actual HTTP requests through Laravel's test client, touch the real `gtms_data` database, and roll back via `DatabaseTransactions`.
   - No hardcoded test outputs or dummy facades were detected.

---

## 3. Caveats & Adversarial Findings

### 3.1 Minor Advisory Finding: Shared Database Fixture Concurrency
- **Location**: `tests/Feature/BranchManagementTest.php` line 46:
  ```php
  $this->adminUser = User::where('role_id', 1)->first() ?: User::factory()->create([...]);
  ```
- **Analysis**: Reusing an existing user row (`where('role_id', 1)->first()`) instead of instantiating an isolated factory user (`User::factory()->create()`) creates a shared-state hazard if multiple test suites are executed concurrently against the same database instance. During parallel execution, concurrent calls to `assignRole('Admin')` on User 1 can cause a `1062 Duplicate entry` on `model_has_roles`.
- **Severity**: **Minor** (Non-blocking).
- **Recommendation**: For future refactoring, use `User::factory()->create(['role_id' => 1])` with unique email generators for all test fixtures.

### 3.2 Nullable Field Overwriting on Update
- **Location**: `app/Http/Controllers/BranchController.php` lines 58–60 (`city`, `state`, `pincode`).
- **Analysis**: If an update payload omits optional fields, they are overwritten with `null`. This is consistent with existing controller behavior, but callers should ensure complete payloads are submitted.

---

## 4. Conclusion

- **Gate Verdict**: **APPROVE**
- Requirements **R1** (Branch Management) and **R4** (Multi-Tenancy Scope) are 100% satisfied with 29 high-fidelity tests (354 assertions), zero regressions on existing suites (150 assertions), verified zero unhandled 500 exceptions, and sound database transaction safety.

---

## 5. Verification Method

To independently verify the test suites and audit results, execute:

1. **Combined Branch & Multi-Tenancy Suites**:
   ```powershell
   php artisan test tests/Feature/BranchManagementTest.php tests/Feature/MultiTenancyBranchScopeTest.php
   ```
   *Expected: 29 passed (354 assertions)*

2. **Regression Check**:
   ```powershell
   php artisan test --filter=ApplicationHandlersAndPaymentsTest
   php artisan test --filter=PptDgpsAndEcComplianceTest
   ```
   *Expected: 12 passed (150 assertions)*

# Adversarial Verification & Challenge Report — Branch Management & Multi-Tenancy Scope

## Verdict: REQUEST_CHANGES

---

## 1. Observation

### 1.1 Test Suite Execution Logs
Direct execution of target feature tests and regression suites:

```powershell
php artisan test tests/Feature/BranchManagementTest.php tests/Feature/MultiTenancyBranchScopeTest.php
```
**Output:**
```text
   PASS  Tests\Feature\BranchManagementTest
  ✓ guest redirected from all branch endpoints                                                   0.54s  
  ✓ unauthorized user forbidden on branch view                                                   0.30s  
  ✓ unauthorized user forbidden on branch create                                                 0.29s  
  ✓ unauthorized user forbidden on branch edit                                                   0.25s  
  ✓ unauthorized user forbidden on branch delete                                                 0.14s  
  ✓ authorized user can view branch index                                                        0.19s  
  ✓ branch index renders both active and inactive branches                                       0.40s  
  ✓ user management filters inactive branches for department assignment                          0.54s  
  ✓ branch creation validation errors                                                            0.69s  
  ✓ branch creation successful persistence                                                       0.21s  
  ✓ branch update validation errors                                                              0.45s  
  ✓ branch update metadata successfully                                                          0.51s  
  ✓ toggle branch status to inactive and active                                                  0.19s  
  ✓ delete branch successfully                                                                   0.31s  
  ✓ delete branch nullifies child application branch id                                          0.54s  
  ✓ zero unhandled 500 on update with missing or invalid id                                      0.76s  
  ✓ zero unhandled 500 on destroy with missing or invalid id                                     0.48s  

   PASS  Tests\Feature\MultiTenancyBranchScopeTest
  ✓ non admin user restricted to own branch records                                              0.56s  
  ✓ non admin cannot query or find other branch record                                           0.32s  
  ✓ super admin bypasses branch scope via role id one                                            0.62s  
  ✓ admin bypasses branch scope via spatie admin role                                            0.17s  
  ✓ super admin spatie role bypasses branch scope                                                0.40s  
  ✓ user with null branch id is unscoped                                                         0.47s  
  ✓ unauthenticated context is unscoped                                                          1.57s  
  ✓ model creation auto assigns authenticated non admin user branch id when empty                0.16s  
  ✓ explicit branch id is preserved on model creation                                            0.38s  
  ✓ without global scope bypasses branch scope                                                   0.40s  
  ✓ all eight models implement belongs to branch and register branch scope                       0.35s  
  ✓ cross model branch isolation on mineral stockpile                                            0.34s  

  Tests:    29 passed (230 assertions)
  Duration: 12.86s
```

```powershell
php artisan test tests/Feature/ApplicationHandlersAndPaymentsTest.php tests/Feature/PptDgpsAndEcComplianceTest.php
```
**Output:**
```text
   PASS  Tests\Feature\ApplicationHandlersAndPaymentsTest
  ✓ schema integrity and model relations                                                         0.54s  
  ✓ lease application step6 handlers workflow                                                    0.19s  
  ✓ lease application step7 payment workflow and calculation                                     0.08s  
  ✓ lease application step8 review displays handlers and payment                                 0.09s  
  ✓ mining application intake saves handlers and payment                                         0.53s  
  ✓ payment ledger status derivation logic                                                       0.05s  

   PASS  Tests\Feature\PptDgpsAndEcComplianceTest
  ✓ ppt department index and dossier                                                             0.27s  
  ✓ ppt department wizard steps                                                                  0.35s  
  ✓ dgps survey index and dossier                                                                0.18s  
  ✓ dgps survey wizard steps                                                                     0.55s  
  ✓ ec compliance index and dossier                                                              0.48s  
  ✓ ec compliance wizard steps                                                                   0.73s  

  Tests:    12 passed (150 assertions)
  Duration: 4.36s
```

### 1.2 Implementation Observations

1. **`app/Http/Controllers/BranchController.php` (lines 18–34, 45–64)**:
   ```php
   $request->validate([
       'branch_name' => 'required',
       'contact_person' => 'required',
       'mobile' => 'required',
       'address' => 'required',
   ]);
   ```
   No `string` type cast, no `max:255` length on `branch_name` and `contact_person`, no `max:15` on `mobile`, no `max:10` on `pincode`. `city`, `state`, and `pincode` are assigned directly (`$branch->city = $request->city; $branch->state = $request->state; $branch->pincode = $request->pincode;`) without any validation rules.

2. **`database/migrations/2026_07_07_093905_create_branches_table.php` (lines 16–24)**:
   ```php
   $table->string('branch_name');
   $table->string('contact_person')->nullable();
   $table->string('mobile', 15)->nullable();
   $table->text('address')->nullable();
   $table->string('city')->nullable();
   $table->string('state')->nullable();
   $table->string('country')->default('India');
   $table->string('pincode', 10)->nullable();
   ```
   Column `mobile` is `VARCHAR(15)`. Column `pincode` is `VARCHAR(10)`. Column `branch_name` is `VARCHAR(255)`.
   In MySQL strict mode (`'strict' => true` in `config/database.php`), any value exceeding the column length causes `SQLSTATE[22001]: String data, right truncated: 1406 Data too long for column` (QueryException, unhandled HTTP 500).

3. **`app/Models/Scopes/BranchScope.php` (lines 17–23)**:
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
   If a non-admin user has `branch_id = null`, `empty($user->branch_id)` evaluates to `true`. The `where` clause is **skipped entirely**. This was codified as expected behavior in `MultiTenancyBranchScopeTest.php` line 297 (`test_user_with_null_branch_id_is_unscoped`), granting full statewide data access to unassigned non-admin users.

4. **`app/Models/Traits/BelongsToBranch.php` (lines 19–26)**:
   ```php
   static::creating(function ($model) {
       if (Auth::hasUser() && empty($model->branch_id)) {
           $user = Auth::user();
           if (!empty($user->branch_id)) {
               $model->branch_id = $user->branch_id;
           }
       }
   });
   ```
   The `empty($model->branch_id)` guard only sets `$model->branch_id` if the attribute is empty. If a non-admin scoped user explicitly provides `branch_id = 2` in the request payload, the model retains `branch_id = 2`, creating a cross-tenant record injection.

5. **`app/Http/Controllers/BranchController.php` (lines 73–87)**:
   `destroy()` deletes the branch. Database foreign keys on 8 operational modules (`lease_applications`, `mining_applications`, `ppt_applications`, `dgps_surveys`, `drone_surveys`, `environment_projects`, `ec_compliances`, `mineral_stockpiles`) correctly execute `nullOnDelete()`. However, `users.branch_id` has no foreign key constraint (`2026_07_06_114649_add_role_id_and_branch_id_to_users_table.php`), leaving users assigned to deleted branch IDs trapped in an empty scope.

---

## 2. Logic Chain

1. **Premise 1 (Input Validation & 500 Prevention)**:
   - Acceptance criteria require: *"Zero unhandled 500 exceptions across GET/POST routes for /branch, /roles, /user"* and resilience against *"extremely long string inputs"*.
   - Observation 1.2.1 and 1.2.2 show that `BranchController::store` and `BranchController::update` lack length constraints (`max:15` on mobile, `max:10` on pincode, `max:255` on branch_name) and type constraints (`string`).
   - In MySQL strict mode, inserting a 20-character string into `mobile` (VARCHAR 15) or a 20-character string into `pincode` (VARCHAR 10) triggers a PDO `1406 Data too long` QueryException. Because this exception is uncaught in `BranchController`, Laravel returns an HTTP 500 Internal Server Error rather than an HTTP 422 validation response.

2. **Premise 2 (Multi-Tenancy Isolation — Fail-Open vs Fail-Closed)**:
   - The security goal of `BranchScope` is multi-tenancy isolation: non-admin users must only access records within their assigned branch.
   - Observation 1.2.3 shows that when `user->branch_id` is `null`, `BranchScope` applies zero WHERE clauses to queries across all 8 multi-tenant models.
   - Any staff user created without an assigned branch (or whose branch was deleted) bypasses all tenant filtering and gains full statewide access to sensitive customer lease applications, mining dossiers, and financial ledgers. This is a classic "fail-open" security defect.

3. **Premise 3 (Cross-Tenant Record Injection)**:
   - Observation 1.2.4 demonstrates that `BelongsToBranch::creating` checks `empty($model->branch_id)`.
   - A non-admin user belonging to Branch A who crafts a POST request specifying `branch_id = B` bypasses automatic assignment and writes records directly into Branch B's tenancy. While the user cannot subsequently read the injected record, they can forge data into foreign branches.

4. **Premise 4 (Scope Isolation under Joins & Subqueries)**:
   - Observation 1.2.3 shows `$model->getTable() . '.branch_id'`.
   - In `LeaseApplication::join('branches', ...)` and `Customer::with('leaseApplications')`, the table prefix prevents SQL 1052 ambiguous column collisions.
   - `whereHas` subqueries correctly inherit `BranchScope`, preventing tenant existence probing.

---

## 3. Caveats

- **Scope of Controller Audit**: Probing focused on `BranchController`, `BranchScope`, and `BelongsToBranch`. While existing tests passed for `ApplicationHandlersAndPaymentsTest` and `PptDgpsAndEcComplianceTest`, individual intake controllers across other modules (e.g. `MiningController`) were not modified and may have independent authorization checks that mitigate cross-tenant creation.
- **MySQL Configuration**: The string truncation 500 error depends on MySQL running in strict mode (`STRICT_TRANS_TABLES`), which is the default in Laravel 12 / GTMS configuration (`config/database.php`). On non-strict legacy MySQL engines, values would be silently truncated without throwing 500.

---

## 4. Conclusion

The existing implementation has achieved 100% test passing (41/41 tests across all 4 suites) and solid coverage for standard authorization, CRUD operations, and SQL join isolation. However, under adversarial review, three specific failure modes and security risks were identified:

1. **[HIGH] Fail-Open Scope for Unassigned Staff**: `BranchScope.php` grants full statewide data access to non-admin users if `branch_id` is null.
2. **[MEDIUM] Cross-Tenant Record Injection**: `BelongsToBranch.php` permits non-admin users to explicitly specify another branch's `branch_id` upon creation.
3. **[MEDIUM] Unhandled 500 on Input String Length Overflow**: `BranchController.php` lacks length validation (`max:15` for mobile, `max:10` for pincode, `max:255` for names) and type validation (`string`), causing unhandled 500 PDO exceptions in strict MySQL mode.
4. **[LOW] Orphaned User Branch Reference**: `BranchController::destroy` deletes branches without nullifying `users.branch_id`.

**Verdict: REQUEST_CHANGES**

### Actionable Remediation Plan for Implementation Agent:

1. **In `app/Http/Controllers/BranchController.php`**:
   - Update `store()` and `update()` validation rules:
     ```php
     $request->validate([
         'branch_name'    => 'required|string|max:255',
         'contact_person' => 'required|string|max:255',
         'mobile'         => 'required|string|max:15',
         'address'        => 'required|string',
         'city'           => 'nullable|string|max:255',
         'state'          => 'nullable|string|max:255',
         'pincode'        => 'nullable|string|max:10',
     ]);
     ```
   - In `destroy()`, nullify `users.branch_id` before deleting the branch:
     ```php
     \App\Models\User::where('branch_id', $branch->id)->update(['branch_id' => null]);
     ```

2. **In `app/Models/Scopes/BranchScope.php`**:
   - Switch from fail-open to fail-closed: If a non-admin user has NO branch assigned, restrict queries to zero records (or records with null branch_id) rather than granting statewide access:
     ```php
     if (Auth::hasUser()) {
         $user = Auth::user();
         $isAdmin = $user->role_id === 1 || $user->hasRole(['Admin', 'Super Admin']);
         if (!$isAdmin) {
             if (!empty($user->branch_id)) {
                 $builder->where($model->getTable() . '.branch_id', $user->branch_id);
             } else {
                 // Fail-closed: unassigned non-admins cannot view any tenant data
                 $builder->whereRaw('1 = 0');
             }
         }
     }
     ```
   - *Note*: If the business specifically requires unassigned staff to have statewide access, this must be explicitly confirmed and documented as a deliberate business exception in `docs/22-unknowns-risks.md`.

3. **In `app/Models/Traits/BelongsToBranch.php`**:
   - For non-admin users, ALWAYS enforce `$model->branch_id = $user->branch_id` during creation to prevent cross-tenant branch spoofing:
     ```php
     static::creating(function ($model) {
         if (Auth::hasUser()) {
             $user = Auth::user();
             $isAdmin = $user->role_id === 1 || $user->hasRole(['Admin', 'Super Admin']);
             if (!$isAdmin && !empty($user->branch_id)) {
                 $model->branch_id = $user->branch_id;
             } elseif (empty($model->branch_id) && !empty($user->branch_id)) {
                 $model->branch_id = $user->branch_id;
             }
         }
     });
     ```

4. **In `tests/Feature/BranchManagementTest.php`**:
   - Add test cases asserting that string length overflows (`mobile` with 20 chars, `pincode` with 20 chars, `branch_name` with 300 chars) return HTTP 422 JSON validation errors and never HTTP 500.

---

## 5. Verification Method

To verify these findings and any subsequent fixes:

1. **Run Current Test Suites**:
   ```powershell
   php artisan test tests/Feature/BranchManagementTest.php tests/Feature/MultiTenancyBranchScopeTest.php tests/Feature/ApplicationHandlersAndPaymentsTest.php tests/Feature/PptDgpsAndEcComplianceTest.php
   ```
2. **Inspect Validation Rules**:
   Inspect `app/Http/Controllers/BranchController.php` lines 18-23 and 45-51 to ensure `max:15`, `max:10`, `max:255`, and `string` rules are in place.
3. **Inspect Scope Logic**:
   Inspect `app/Models/Scopes/BranchScope.php` lines 17-23 to verify fail-closed handling for non-admin users without a branch assignment.

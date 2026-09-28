# Orchestrator 2 Execution Plan: GTMS Auth & Admin Automated Test Suite

## Objective
Execute a comprehensive architectural audit, end-to-end route verification, and automated feature test suite for the GTMS Authentication and Administration modules:
1. Department / Branch (`/branch`, `BranchController`)
2. Roles & Permissions (`/roles`, `RolesController`)
3. User Management (`/user`, `UserController`, `AuthController`)
4. Multi-Tenancy Scope Verification (`BranchScope`)

## Verification & Acceptance Criteria
- All new tests pass with `php artisan test`
- Zero regressions across existing test suites (`ApplicationHandlersAndPaymentsTest`, `PptDgpsAndEcComplianceTest`)
- Zero unhandled 500 exceptions across GET/POST routes for `/branch`, `/roles`, `/user`
- Forensic Audit Clean verdict (no mock bypasses, no hardcoded cheating, genuine assertions against database & response lifecycle)

## Step-by-Step Plan

### Step 1: Architectural Exploration & Ground-Truth Mapping
- Dispatch 2 Explorers in parallel to inspect:
  - Explorer A: `BranchController`, `RolesController`, `RolePermissionSeeder`, routes in `routes/web.php` for `/branch` and `/roles`, views, and current database migrations for departments/branches and roles/permissions.
  - Explorer B: `UserController`, `AuthController`, `BranchScope`, `User` model, dual-login (`email` vs `user_code`), `user_code` generation, dual role sync (`role_id` and Spatie `model_has_roles`), image upload handling, deletion safeguards, routes in `routes/web.php` for `/user` and `/login`, and existing test suites (`ApplicationHandlersAndPaymentsTest`, `PptDgpsAndEcComplianceTest`).
- Collect reports, synthesize exact route list, controller methods, validation rules, error handling, and test harness setup (`DatabaseTransactions`, authentication helpers, test user setup).

### Step 2: Test Suite Architecture & Implementation
- Dispatch specialist Workers (using testing patterns and test database transaction practices) to author comprehensive feature tests:
  - `tests/Feature/BranchManagementTest.php`:
    - Directory viewing with active/inactive filtering
    - Branch creation with validation
    - Branch updating with metadata and status
    - Branch deletion and DB integrity
  - `tests/Feature/RolesAndPermissionsTest.php`:
    - Viewing role matrix and assigned permission counts
    - Creating new roles and syncing permissions
    - Dynamic AJAX fetching of role permissions
    - Updating role names and permission sets
    - Destruction safeguards (Super Admin & Admin non-deletable)
  - `tests/Feature/UserManagementAndAuthTest.php`:
    - Dual-identifier login (`email` and `user_code`)
    - User provisioning with auto `user_code` generation
    - Dual role synchronization (`users.role_id` vs Spatie `model_has_roles`)
    - Avatar image uploading and unlinking on replacement
    - Self-deletion and last-admin deletion protections
    - Permission middleware gating across CRUD routes
  - `tests/Feature/MultiTenancyBranchScopeTest.php`:
    - `BranchScope` restricting non-admin users to their assigned `branch_id`
    - Super Admin / Admin statewide unrestricted access bypass via `hasRole(['Admin', 'Super Admin'])`
- Ensure any subtle route bugs / 500 errors in controllers discovered during testing are remedied cleanly without regressions.

### Step 3: Adversarial Review & Empirical Verification
- Dispatch 2 Reviewers independently to audit:
  - Test coverage completeness against R1, R2, R3, R4
  - Absence of brittle tests or false positives
  - Execution of `php artisan test` on all test files
- Dispatch 2 Challengers to test edge cases:
  - Concurrent user code generation collisions
  - Boundary permissions and unauthorized route access (403 vs 500)
  - Soft-deleted branch/user referential integrity

### Step 4: Forensic Audit Integrity Gating
- Dispatch `teamwork_preview_auditor` to verify:
  - Genuine database mutations and assertions (using `DatabaseTransactions`)
  - No dummy pass mocks or hardcoded return bypasses
  - Authentic execution of routes, middleware, and Eloquent queries
- Gate check: strict pass required.

### Step 5: Regression & Full Suite Execution
- Run full test suite: `tests/Feature/BranchManagementTest.php`, `tests/Feature/RolesAndPermissionsTest.php`, `tests/Feature/UserManagementAndAuthTest.php`, `tests/Feature/MultiTenancyBranchScopeTest.php`, `tests/Feature/ApplicationHandlersAndPaymentsTest.php`, `tests/Feature/PptDgpsAndEcComplianceTest.php`.
- Verify 100% passing tests with 0 failures, 0 regressions, 0 unhandled 500 errors.

### Step 6: Final Reporting & Handoff to Sentinel
- Update `GATE_STATUS.md`, `progress.md`, and generate `handoff.md`.
- Report completion to Sentinel with full metric summary.

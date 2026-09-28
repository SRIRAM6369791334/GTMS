# Scope: GTMS Authentication and Administration Test Suite & Audit

## Architecture
- GTMS Laravel 12 ERP:
  - Branch Module: `app/Http/Controllers/BranchController.php`, `app/Models/Branch.php`, `routes/web.php` (`/branch/*`)
  - Roles & Permissions: `app/Http/Controllers/RolesController.php`, Spatie Laravel-Permission (`Role`, `Permission`), `RolePermissionSeeder.php`, `routes/web.php` (`/roles/*`)
  - User Management & Auth: `app/Http/Controllers/UserController.php`, `app/Http/Controllers/AuthController.php`, `app/Models/User.php`, `routes/web.php` (`/user/*`, `/login`, `/logout`)
  - Multi-Tenancy Scoping: `app/Models/Scopes/BranchScope.php`, `app/Traits/HasBranchScope.php` (applied on domain models)

## Feature Inventory
| # | Feature | Description | Milestone | Source |
|---|---------|-------------|-----------|--------|
| 1 | Branch Directory & Filtering | Viewing branch list with active/inactive status filter | M1 | R1 |
| 2 | Branch Creation & Validation | Adding branches with name, code, contact details and validation | M1 | R1 |
| 3 | Branch Update & Status Toggle | Updating branch metadata, activation and deactivation | M1 | R1 |
| 4 | Branch Deletion & Integrity | Deletion safeguards and database integrity | M1 | R1 |
| 5 | Role Matrix & Permissions View | Viewing roles table and assigned permission count | M2 | R2 |
| 6 | Role Creation & Permission Sync | Creating new roles and synchronizing permission IDs | M2 | R2 |
| 7 | Dynamic AJAX Permissions | Fetching permissions dynamically for role editing/viewing | M2 | R2 |
| 8 | Role Update & Sync | Updating role title and syncing new permission sets | M2 | R2 |
| 9 | Role Destruction Safeguards | Preventing deletion of critical roles (Admin, Super Admin) | M2 | R2 |
| 10 | Dual-Identifier Authentication | Logging in via canonical email OR user_code (e.g., LUK_001) | M3 | R3 |
| 11 | User Provisioning & Code Gen | Automatic sequential/pattern generation of user_code | M3 | R3 |
| 12 | Dual Role Synchronization | Ensuring users.role_id and model_has_roles stay in sync | M3 | R3 |
| 13 | Avatar Handling & Unlinking | Uploading user avatars and safely unlinking old avatars | M3 | R3 |
| 14 | User Deletion Safeguards | Self-deletion prevention and last-admin deletion block | M3 | R3 |
| 15 | RBAC Middleware Gating | Verifying permission middleware across all user/role/branch routes | M3 | R3 |
| 16 | Multi-Tenancy BranchScope | Restricting non-admin queries to assigned branch_id | M4 | R4 |
| 17 | Super Admin BranchScope Bypass | Granting statewide access to Admin / Super Admin via hasRole | M4 | R4 |

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| M0 | Survey & Exploration | Comprehensive exploration of controllers, models, routes, seeders, and existing tests | none | DONE |
| M1 | Branch Feature Test Suite | Author and verify BranchManagementTest covering all R1 features | M0 | IN_PROGRESS |
| M2 | Roles & Permissions Test Suite | Author and verify RolesAndPermissionsTest covering all R2 features | M0 | IN_PROGRESS |
| M3 | User Lifecycle & Auth Test Suite | Author and verify UserManagementAndAuthTest covering all R3 features | M0 | IN_PROGRESS |
| M4 | Multi-Tenancy Scope Test Suite | Author and verify MultiTenancyBranchScopeTest covering all R4 features | M0 | IN_PROGRESS |
| M5 | Comprehensive Audit, Gating & Zero Regression | Full test suite execution, reviews, challenge tests, forensic integrity audit | M1, M2, M3, M4 | PLANNED |

## Interface Contracts
### AuthController ↔ User
- `login(Request $request)`: accepts `login` (or `email`/`username`/`user_code`) and `password`.
- Validates either email format or `user_code` format.

### RolesController ↔ Spatie Role/Permission
- `store(Request $request)`: creates `Role` and executes `$role->syncPermissions(...)`.
- `update(Request $request, $id)`: updates name and synchronizes permissions.
- `destroy($id)`: checks `$role->name` to reject Admin / Super Admin deletion.

### UserController ↔ User / Branch / Role
- `store(Request $request)`: generates `user_code`, assigns `branch_id`, assigns `role_id`, calls `$user->assignRole($role)`.
- `update(Request $request, $id)`: updates profile, handles avatar upload/unlink, syncs `role_id` and Spatie roles.
- `destroy($id)`: prevents `Auth::id() === $id` and prevents deleting the last active Admin.

### BranchScope ↔ Scoped Models
- Automatically applies `where('branch_id', $user->branch_id)` when user is not Admin/Super Admin.

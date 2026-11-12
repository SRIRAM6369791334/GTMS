# 14 — User Roles & Permissions

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Security: [18-security-audit.md](18-security-audit.md).

## 1. Mechanism — VERIFIED
* **Spatie laravel-permission v6**, guard `web`. Roles are assigned through `model_has_roles`. `users.role_id` is **also** kept in sync by `UserController` (`syncRoles([$role->name])`). The two must agree: the `BranchScope` checks `role_id !== 1`, while most other code checks `hasRole('Admin'|'Super Admin')`.
* **Super-user bypass:** `Gate::before(fn($user) => $user->hasRole(['Admin','Super Admin']) ? true : null)` (`AppServiceProvider::boot`). Every `permission:` middleware and `@can` passes for these roles.
* **Branch isolation:** see [06](06-model-analysis.md) §1. Admin / Super Admin, or a user with `role_id = 1`, see all branches.
* Permission caching is cleared after role changes (`PermissionRegistrar::forgetCachedPermissions`).

## 2. Permission catalogue — VERIFIED from `database/seeders/RolePermissionSeeder.php`
Pattern `<module>.<action>`:

| Module key | Actions seeded | Used by routes |
|---|---|---|
| dashboard | view | `/dashboard` |
| branch, roles, users, category, product, unit | view, create, edit, delete | admin screens (`unit.create/edit/delete` and `product.edit/delete` are unused by routes) |
| customer | view, create, edit, delete | directory + tracking |
| application (lease) | view, create, edit, delete | lease (`delete` unused) |
| mining | view, create, edit, delete | mining (`delete` unused) |
| environment | view, create, edit, delete | environment view; create / edit only as alternatives on upload routes |
| environment.b2 | view, create, upload, review, status | environment store / upload / review / status / submit-to-PPT (`view`, `status` unused by routes) |
| ec_certificate, ec_compliance, ppt, dgps, drone | view, create, edit, delete, manage | per module (`delete`, `manage` unused by routes) |
| account | view, create, edit, delete | accounts |
| notification | view, manage | **unused** (notification routes are auth-only) |

Permissions that exist but guard nothing give a false sense of control. For example, revoking `notification.view` has no effect.

## 3. Seeded roles — PARTIALLY VERIFIED (seeder read in part)
| Role | Grants |
|---|---|
| Super Admin | All (via `Gate::before`) |
| Admin | All (via `Gate::before`). `RolesController@update` force-resyncs Admin / Super Admin to all permissions |
| Staff | View-only set: dashboard, customer, application, mining, environment, users, ec_certificate, ec_compliance, ppt, dgps, drone, account, notification, category, product, unit (`RolePermissionSeeder.php` ~163-182) |
| Officer | A broader operational set (lines ~185+; not fully read) |
The seeder also creates a default Super Admin account and a staff test account with **hard-coded credentials** (values deliberately not reproduced here). Rotate them on any real deployment.

## 4. Delegated administration rules (non-admin holders of users.* / roles.*) — VERIFIED
* They see only roles they created (`roles.created_by = me`) and users they created (`users.user_id = me`) or themselves.
* They can create roles only with permissions they hold. Reserved names (admin, super admin, superadmin) are blocked.
* They can assign only their own roles, only subsets of their own permissions, and only their own branch. They cannot touch Admin / Super Admin accounts.
* Nobody can change their own role. The last Admin / Super Admin cannot be deleted.
* Gap: roles with `created_by = NULL` pass the "created by other administrators" checks (`!empty($role->created_by) && …`). The migration set existing roles to `created_by = 1`, so this only affects roles created later without an authenticated user (INFERRED).

## 5. Route-level gaps — VERIFIED
| Endpoint group | Protection | Effect |
|---|---|---|
| `/api/upload/chunk/*` | **none** | unauthenticated |
| `/master-pipeline/*` | auth only | any logged-in user can forward / return / resubmit any visible master project |
| `/accounts/invoices/seal-*` | auth only | any logged-in user can replace / remove the invoice seal |
| `/notifications/*` | auth only | own data only (OK), but `notification.*` permissions are meaningless |
| Lease validate **and** approve | same `application.edit` | no segregation of duties |
| Mining `advanceStage` | `mining.edit` | arbitrary stage value |
| `*/updateStatus` (env, PPT, EC cert, EC compliance) | module edit / review | free-text status |

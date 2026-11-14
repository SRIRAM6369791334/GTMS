# 🔑 GTMS System Credentials & Test Guide
> **Token-Saver Reference:** Keep this guide handy for quick test execution, demo credentials, database connection parameters, and artisan administration commands without inspecting `.env` or test classes.

---

## 1. System Access & Demo Credentials

| Role / Profile | Canonical Email | Personnel Code | Password | Assigned Branch | Primary Scopes & Permissions |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@gtms.com` | `LUK_001` | `admin123` | Salem Regional Office | Full Statewide 100% System Access (all 18 modules) |
| **Test Staff** | `staff_unit_test@gtms.com`| `LUK_002` | `staff123` | Salem Regional Office | Standard Operational Staff (Scoped to branch) |
| **Field Officer**| `officer@gtms.com` | `LUK_003` | `officer123` | Salem Regional Office | DGPS & Drone Technical Surveyor |

---

## 2. Local Database & Server Environment

* **Database Connection:** MySQL
* **Host / Port:** `127.0.0.1:3306`
* **Database Name:** `gtms_data`
* **Username:** `root`
* **Password:** *(empty)*
* **Web Server URL:** `http://127.0.0.1:8000` or `http://127.0.0.1:8002` (E2E)

---

## 3. High-Value Test Suite Commands

Run these targeted PHPUnit test suites during development to ensure zero regressions:

### User & Authentication:
```powershell
php artisan test --filter="UserManagementAndAuthTest"
```

### Roles & RBAC Matrix:
```powershell
php artisan test --filter="RolesAndPermissionsTest"
php artisan test --filter="RolePermissionAuditTest"
```

### Regional Branches & Multi-Tenancy Scoping:
```powershell
php artisan test --filter="BranchManagementTest"
php artisan test --filter="MultiTenancyBranchScopeTest"
```

### Security & Privilege Escalation Checks:
```powershell
php artisan test --filter="PrivilegeEscalationSecurityTest"
php artisan test --filter="SecurityHardeningTest"
```

### Master Pipeline & Chunked File Upload:
```powershell
php artisan test --filter="MasterPipelineWorkflowTest"
php artisan test --filter="ChunkedUploadPipelineTest"
```

---

## 4. Key Database Seeders

To restore or seed specific modules without dropping the entire database:

| Seeder Class | Command | What It Seeds |
| :--- | :--- | :--- |
| **RolePermissionSeeder** | `php artisan db:seed --class=RolePermissionSeeder` | All 78 Spatie permissions & default Admin roles |
| **CustomerSeeder** | `php artisan db:seed --class=CustomerSeeder` | 5 Standard Verified Tamil Nadu Mining Customers |
| **GtmsMasterDataSeeder** | `php artisan db:seed --class=GtmsMasterDataSeeder` | 38 TN Districts, Minerals, Nature of Works |
| **PptAndDgpsModuleSeeder**| `php artisan db:seed --class=PptAndDgpsModuleSeeder` | Document checklist taxonomies and folders |

---

## 5. System Cache & Maintenance Commands

Whenever routes, permissions, or configurations change, run this reset pipeline:

```powershell
# Reset Laravel Cache & Optimization
php artisan optimize:clear

# Clear Spatie Cached Permissions
php artisan permission:cache-reset

# Verify Web Route List
php artisan route:list --path=""
```

---

## 6. Security Hygiene Checklist
1. **Never Log Sensitive Data:** Passwords, decrypted MIMAS keys, and auth tokens must never be written to logs or serialized in responses.
2. **Always Use CSRF on AJAX:** All `$.ajax` calls must include `headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }`.
3. **Always Clean Up Deleted Files:** Use `unlink()` on old files when models are updated or deleted to avoid server disk bloat.
4. **Enforce Branch Scoping on New Models:** Ensure every operational model implements `belongsTo(Branch::class)` and auto-attaches `created_by = auth()->id()`.

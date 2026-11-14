# 🛠️ GTMS Troubleshooting & Common Errors Playbook
> **Token-Saver Reference:** Consult this playbook whenever an HTTP error (419, 403, 500), UI freeze, or database exception occurs. Contains instant root causes, diagnosis steps, and verified 1-line fixes.

---

## 1. HTTP 419 — Page Expired (CSRF Token Mismatch)

### Symptoms:
* AJAX form submission fails silently or returns `{"message": "CSRF token mismatch."}` with HTTP status `419`.

### Root Cause:
* The AJAX request is missing the Laravel CSRF token header, or the session cookie expired.

### Instant Fix:
Ensure your JavaScript file initializes `$.ajaxSetup()` at the very top of `$(document).ready()`:

```javascript
// public/js/ajax/your_module.js
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});
```
And verify that the Blade layout (`resources/views/layouts/app.blade.php`) contains:
```html
<meta name="csrf-token" content="{{ csrf_token() }}">
```

---

## 2. HTTP 403 — Unauthorized / Missing Permission

### Symptoms:
* User receives `User does not have the right permissions` or `This action is unauthorized` (HTTP `403`).

### Root Causes & Fixes:

#### Case A: Stale Spatie Permission Cache
When new permissions are added or synced, the Redis/file cache must be cleared.
* **Fix Command:**
  ```powershell
  php artisan permission:cache-reset
  php artisan cache:clear
  ```

#### Case B: Non-SuperAdmin Privilege Escalation Lock
In `UserController` or `RolesController`, non-superadmins are strictly blocked from:
* Assigning `Admin` or `Super Admin` roles.
* Editing roles or users created by other administrators.
* Modifying their own security role.
* **Diagnosis:** Check if the logged-in user is `admin@gtms.com` (Super Admin) or standard staff. Only Super Admin has statewide unrestricted access.

---

## 3. HTTP 403 — Multi-Tenancy Branch Scope Rejection

### Symptoms:
* A non-admin user cannot view, edit, or delete an application or customer, receiving `Record not found` or `Access Denied`.

### Root Cause:
* `MultiTenancyBranchScope` automatically filters models (`Customer`, `LeaseApplication`, etc.) to `WHERE branch_id = auth()->user()->branch_id`. If the record belongs to Branch 2 (e.g., Coimbatore) and the officer belongs to Branch 1 (Salem), access is automatically restricted.

### Fix / Verification:
* Log in as Super Admin (`admin@gtms.com`), who automatically bypasses the global branch scope:
  ```php
  // In tests or background services:
  $record = Customer::withoutGlobalScope('branch_scope')->find($id);
  ```

---

## 4. DataTables Error: "Cannot reinitialise DataTable"

### Symptoms:
* Browser console shows: `Uncaught TypeError: Cannot reinitialise DataTable on table #example10`.
* Pagination, search, or sorting stops responding.

### Root Cause:
* GTMS includes a global `datatables.init.js` in vendor scripts that automatically initializes `#example10`. When your page-specific script initializes `#example10` a second time, DataTables throws a fatal error.

### Instant Fix:
Always destroy any prior initialization before re-instantiating the DataTable:

```javascript
if ($.fn.DataTable.isDataTable('#example10')) {
    $('#example10').DataTable().destroy();
}

var table = $('#example10').DataTable({
    responsive: true,
    pageLength: 10,
    searching: true,
    dom: 't<"d-flex justify-content-between align-items-center p-3"ip>'
});
```

---

## 5. UI Freeze / Infinite Preloader Spinner

### Symptoms:
* Page loads, but a white screen or spinning loader overlay stays indefinitely, blocking all user clicks.

### Root Cause:
* The `#preloader` element is waiting for window load events, or a JavaScript error halted execution before the dismissal hook fired in `dlabnav-init.js`.

### Instant Fix:
Include this failsafe snippet in your page scripts or test setup:
```javascript
$(window).on('load', function () {
    $('#preloader').fadeOut(200, function () { $(this).remove(); });
    $('#main-wrapper').addClass('show');
});

// Failsafe timeout after 1.5 seconds
setTimeout(function () {
    var p = document.getElementById('preloader');
    if (p) p.remove();
    var mw = document.getElementById('main-wrapper');
    if (mw) mw.classList.add('show');
}, 1500);
```

---

## 6. MIMAS Password Decryption MAC Exception

### Symptoms:
* Calling `Crypt::decryptString($credential->password)` throws `Illuminate\Contracts\Encryption\DecryptException: The payload is invalid` or MAC check failure.

### Root Cause:
* The `APP_KEY` in `.env` was regenerated (`php artisan key:generate`) after the encrypted password was stored in the `mimas_credentials` table. AES-256 cipher cannot decrypt ciphertext generated under a different key.

### Instant Fix:
1. Re-enter the password on `/step2` of the wizard, which re-encrypts the string under the current `APP_KEY`.
2. Never run `php artisan key:generate` on an active environment without migrating existing encrypted credentials.

---

## 7. HTTP 500 — File Upload Memory Exhaustion / Post Max Size

### Symptoms:
* Uploading a 50MB+ GIS Shapefile, Drone Orthomosaic, or CAD file returns HTTP `500` or `POST content length exceeds limit`.

### Root Cause:
* Standard PHP file uploads are constrained by `upload_max_filesize = 20M` and `post_max_size = 25M` in `php.ini`.

### Instant Fix:
* Do NOT use standard `multipart/form-data` for large files.
* Use the built-in **GTMS Resilient Chunked Upload Engine** (`gtms-chunk-uploader.js`), which slices files into 2MB chunks and posts to `/api/upload/chunk/*`.
* It bypasses PHP limits and ensures reliable delivery even on field mobile networks.

---

## 8. MySQL Foreign Key Delete Block ("Cannot delete parent row")

### Symptoms:
* Deleting a branch, user, or role returns an integrity violation error.

### Root Cause & Safeguards:
GTMS intentionally prevents destructive cascading deletions:
1. **Branch Deletion:** Blocked if any user is currently assigned to the branch (`users.branch_id`). Move staff to another branch first.
2. **User Deletion:** Blocked if the user has filed records in any statutory application table (`customers`, `lease_applications`, etc.). Set `status = 0` (Inactive) instead.
3. **Role Deletion:** Blocked if any user is assigned to the role (`$assignedUsersCount > 0`).

---

## 9. Quick Diagnostic Commands

Run these three commands whenever encountering an unexplained error:

```powershell
# 1. Clear all cached configs, routes, views
php artisan optimize:clear

# 2. Reset Spatie RBAC permissions
php artisan permission:cache-reset

# 3. View the latest stack trace in Laravel log
Get-Content storage/logs/laravel.log -Tail 30
```

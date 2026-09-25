# GTMS — Error Handling, Transaction Boundaries & Audit Architecture

**Project Name:** Granite / Mining Tracking Management System (GTMS)  
**Domain:** District Mining Office Management & Statutory Regulatory ERP  
**State / Region:** Tamil Nadu, India  
**Target Architecture:** Monolithic Laravel 12 Enterprise Application  
**Authoritative Source:** Codebase Inspection & Empirical Controller Audit  
**Document Number:** `20` of `23`

---

## 1. Executive Summary

In enterprise statutory compliance applications like GTMS, runtime failures cannot be resolved through simple rollbacks of single database rows. A statutory application involves coordinated updates across:
1. Primary application entities (`lease_applications`, `mining_applications`, `environment_projects`)
2. Polymorphic financial ledgers (`application_payments`)
3. Multi-person project handling teams (`application_handlers`)
4. Physical disk files (`public/uploads/...`)
5. Forensic audit trails (`activity_logs`)

To guarantee absolute data integrity, GTMS implements a defense-in-depth error handling architecture spanning **atomic database transaction boundaries**, **pessimistic concurrency locking**, **polymorphic forensic audit logging**, and **graceful user-facing flash alerts**.

---

## 2. Multi-Layer Resilience Architecture

```
┌────────────────────────────────────────────────────────────────────────┐
│                        HTTP & PRESENTATION LAYER                       │
│  - Form Input Validation (Inline $request->validate)                   │
│  - Deep-Link Session Guardrails (Redirect to Step 1 on empty session)   │
│  - Flash Alerts (SweetAlert2 & Toastr integration)                     │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                      TRANSACTION BOUNDARY LAYER                        │
│  - Closure-based: DB::transaction(function() { ... })                  │
│  - Manual: DB::beginTransaction() -> DB::commit() / DB::rollBack()     │
│  - Concurrency Lock: lockForUpdate() on monotonic sequence codes        │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                      FORENSIC & AUDITING LAYER                         │
│  - Polymorphic ActivityLog::create()                                   │
│  - Captured IP Address, User Agent, Old Values, New Values             │
│  - Immutable Timestamp & Actor ID Attribution                          │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                       STORAGE & LOGGING LAYER                          │
│  - Directory Path Sanitization (preg_replace for safe folder paths)    │
│  - Replacement Unlink (@unlink of replaced draft documents)            │
│  - Daily Rotating Error Logs (storage/logs/laravel-YYYY-MM-DD.log)     │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Database Transaction Boundaries

GTMS utilizes two distinct database transaction patterns tailored to specific workflow profiles.

### 3.1 Pattern A: Closure-Based Transactions (`DB::transaction`)
Closure-based transactions are deployed for complex multi-entity intake submissions where any failure must trigger an immediate, unconditional database rollback.

#### Implementation in `CustomerController.php:1062` (Lease Application Step 8 Final Submission):
```php
DB::transaction(function() use (
    &$appNo, &$submittedAppId, $customer, $district, $category, 
    $mineral, $mineralIds, $otherMineralName, $branch, $user, 
    $request, $draft, $step1, $step2, $step6
) {
    // 1. Generate official sequential application number
    $appNo = $this->generateOfficialAppNumber();

    // 2. Insert or update LeaseApplication
    $leaseApp = LeaseApplication::create([
        'common_id'      => 'GTMS-' . substr($appNo, 3),
        'application_no' => $appNo,
        'customer_id'    => $customer->id,
        'district_id'    => $district->id,
        'status'         => 'under_scrutiny',
        // ...
    ]);

    // 3. Synchronize multi-mineral pivot table
    if (!empty($mineralIds)) {
        $leaseApp->minerals()->sync($mineralIds);
    }

    // 4. Save dynamic survey numbers
    // 5. Transfer uploaded session files to permanent directory
    // 6. Save handling persons (ApplicationHandler)
    // 7. Save financial billing ledger (ApplicationPayment)
    // 8. Write audit record (ActivityLog)
});
```

#### Other Key Closure-Based Boundaries:
* `EnverionsoneController.php:117`: Unified Environment Project creation (B1-SC1, B1-SC2, B2). Handles customer resolution, folder initialization, handlers, and billing inside `DB::transaction`.
* `EnvironmentalB2Controller.php:72`: Category B2 intake and dynamic folder seeding.
* `EcCertificateController.php:394`: EC Certificate creation, project status upgrade to `approved`, and file archive placement.

---

### 3.2 Pattern B: Manual Transactions (`DB::beginTransaction`)
Manual transactions are deployed where custom error trapping, selective logging, or contextual user redirects are required upon failure.

#### Implementation in `DgpsSurveyController.php:178-246`:
```php
DB::beginTransaction();
try {
    // 1. Insert primary DgpsSurvey record
    $survey = DgpsSurvey::create([
        'survey_no'   => $surveyNo,
        'customer_id' => $customerId,
        // ...
    ]);

    // 2. Persist project handling personnel
    foreach ($handlers as $hIdx => $h) {
        ApplicationHandler::create([
            'application_type' => 'dgps',
            'application_id'   => $survey->id,
            'name'             => $h['name'],
            'role'             => $h['role'],
            'sort_order'       => $hIdx + 1,
        ]);
    }

    // 3. Persist billing ledger
    ApplicationPayment::create([
        'application_type' => 'dgps',
        'application_id'   => $survey->id,
        'product_value'    => $val,
        'paid_amount'      => $paid,
        'pending_amount'   => $pending,
        'payment_status'   => $pStatus,
    ]);

    DB::commit();
    session()->forget('dgps_wizard');

    return redirect()->route('dgps-survey.show', $survey->id)
        ->with('success', "DGPS Survey {$survey->survey_no} successfully saved and synchronized!");

} catch (\Exception $e) {
    DB::rollBack();
    
    // Log exception for systems administrators
    Log::error("Failed to save DGPS Survey: " . $e->getMessage(), [
        'user_id' => Auth::id(),
        'trace'   => $e->getTraceAsString(),
    ]);

    return redirect()->back()
        ->withInput()
        ->with('error', 'Error saving DGPS survey: ' . $e->getMessage());
}
```

#### Other Manual Boundaries:
* `EcComplianceController.php:244`: Half-yearly compliance filing, 19-folder checklist generation, handlers, and ledger.
* `PptDepartmentController.php:187`: PPT presentation intake, slide deck associations, and presentation scheduling.

---

## 4. Concurrency Protection & Sequence Generation

A frequent point of failure in statutory ERPs is **sequence collision**—two officers concurrently submitting applications and generating identical reference numbers (e.g. `ENV-B2-2026-0005`).

### Pessimistic Concurrency Locking (`lockForUpdate`)
GTMS resolves this race condition by enforcing pessimistic locking at the database level inside `DB::transaction`.

#### Concrete Implementation (`EnverionsoneController.php:124-128`):
```php
$prefix = "ENV-{$cat}-{$year}-";

// Acquire an exclusive row lock on the highest existing sequence
$lastProject = EnvironmentProject::where('project_code', 'like', $prefix . '%')
    ->withTrashed()
    ->lockForUpdate()
    ->orderByRaw("CAST(SUBSTRING_INDEX(project_code, '-', -1) AS UNSIGNED) DESC")
    ->first();

$seq = $lastProject ? (((int) substr($lastProject->project_code, -4)) + 1) : 1;
$code = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
```

### Critical Architectural Features:
1. `lockForUpdate()`: Issues a `SELECT ... FOR UPDATE` statement in MySQL. Any concurrent transaction attempting to read or write the sequence code must wait until the first transaction commits or rolls back.
2. `withTrashed()`: Ensures that even if a project with sequence `0005` was soft-deleted, the generator detects it and increments to `0006`, preventing a fatal MySQL `1062 Duplicate entry` exception on unique indexes.
3. `CAST(... AS UNSIGNED)`: Avoids lexicographical sorting errors (where `0010` would sort before `0009` under standard string collation).

---

## 5. Forensic Audit Logging via `ActivityLog`

All state transitions, document approvals, user rejections, and cross-module promotions are immutably recorded in the `activity_logs` table via the `App\Models\ActivityLog` Eloquent model.

### 5.1 `ActivityLog` Schema & Model Definition
* **Table:** `activity_logs`
* **Model:** `app/Models/ActivityLog.php`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    public $timestamps = false; // Uses explicit created_at

    protected $fillable = [
        'loggable_type',
        'loggable_id',
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function loggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
```

### 5.2 Recorded Forensic Actions Across Modules

| Module | Triggering Event | Controller File & Line | Recorded Action String | Description & Context |
|---|---|---|---|---|
| **Lease Application** | Scrutiny Status Update | `CustomerController:1596` | `document_status_updated` | Document validation/rejection with remarks |
| **Lease Application** | Application Approval | `CustomerController:1769` | `application_approved` | Lease application approved by Collectorate |
| **Lease Application** | Promoted to Mining | `CustomerController:1735` | `moved_to_mining` | Application promoted; file cloning recorded |
| **Mining Plan** | Advance Stage | `MiningController:977` | `stage_advanced` | Mining plan progressed from Stage 6.X to 6.Y |
| **Mining Plan** | Validate Document | `MiningController:588` | `document_validated` | Technical plate or draft approved |
| **Mining Plan** | Promoted to Env | `MiningController:960` | `moved_to_environment` | Promoted to B1 / B2 clearance |
| **Environment** | Intake Created | `EnverionsoneController:215` | `project_created` | Project registered with initial folders |
| **Environment** | Submit SC1 to PPT | `EnverionsoneController:377` | `sc1_submitted_to_ppt` | Stage 1 ToR submitted to DEAC |
| **Environment** | Submit SC2 to PPT | `EnverionsoneController:508` | `sc2_submitted_to_ppt` | Stage 2 EIA submitted to DEAC |
| **Environment** | Document Review | `EnverionsoneController:567` | `document_reviewed` | Folder document scrutinized |
| **EC Certificate** | Certificate Grant | `EcCertificateController:510` | `certificate_issued` | Final SEIAA EC certificate verified & granted |

---

## 6. Filesystem Error Handling & Storage Resilience

GTMS manages large statutory documents (CAD drawings, KML files, high-resolution orthomosaics) on the local disk. Storage operations enforce rigorous safety mechanisms:

### 6.1 Path & Directory Sanitization
Project codes often contain characters that are invalid or problematic in disk paths (e.g., slashes `/`, hash `#`, exclamation mark `!`).

Before creating storage directories, paths are sanitized using regular expressions:
```php
$cleanCode = preg_replace('/[^A-Za-z0-9_\-]/', '_', $projectCode);
$targetDir = public_path("uploads/ec_certificates/{$cleanCode}");

if (!file_exists($targetDir)) {
    mkdir($targetDir, 0755, true);
}
```

### 6.2 Old File Purge on Replacement
When an applicant or officer uploads a revised document to an existing slot, the controller automatically unlinks the previous file to prevent storage bloat and orphaned files:

```php
if (!empty($draft['step2']['file_path'])) {
    $existingFile = public_path($draft['step2']['file_path']);
    if (file_exists($existingFile) && is_file($existingFile)) {
        @unlink($existingFile);
    }
}
```

---

## 7. User-Facing Error Reporting & Flash Alerts

GTMS utilizes a dual notification system integrated into `resources/views/layouts/app.blade.php`:

### 7.1 Flash Session Architecture
Controllers return user feedback via standard Laravel session flash keys:
* `->with('success', '...')`: Triggers a green modal alert or toast.
* `->with('error', '...')`: Triggers a red error modal dialog.
* `->with('info', '...')`: Triggers an informative blue toast notification.
* `->withErrors($validator)`: Binds validation error messages to the `$errors` bag for inline display.

### 7.2 SweetAlert2 & Toastr Client Binding
In `resources/views/layouts/app.blade.php`, incoming session flashes are automatically converted to rich client alerts:

```javascript
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: "{{ session('success') }}",
        timer: 4000,
        showConfirmButton: false
    });
@endif

@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Action Failed',
        text: "{{ session('error') }}",
        confirmButtonColor: '#d33'
    });
@endif

@if(session('info'))
    toastr.info("{{ session('info') }}", "System Notification", { timeOut: 5000 });
@endif
```

---

## 8. Server Error Logging & Diagnostics

### 8.1 Log Configuration
Configured in `config/logging.php`:
* **Default Channel:** `daily` (or `stack` wrapping `daily`)
* **Log Storage Location:** `storage/logs/laravel-YYYY-MM-DD.log`
* **Retention Policy:** 30 days (`LOG_DAILY_DAYS=30`)

### 8.2 Standardized Error Diagnostic Entry
When catching unhandled exceptions in production, controllers write structured diagnostic context:

```php
Log::error('Statutory Workflow Exception Encountered', [
    'module'        => 'MiningPlan',
    'action'        => 'advanceStage',
    'application_id'=> $id,
    'user_id'       => Auth::id(),
    'user_ip'       => request()->ip(),
    'error_message' => $e->getMessage(),
    'file'          => $e->getFile(),
    'line'          => $e->getLine(),
]);
```
This ensures developers and system administrators can immediately diagnose errors without exposing raw PHP stack traces or database schema details to end users.

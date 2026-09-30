# GTMS Accounts & Financial Management Module — Comprehensive Specification & Code Survey Report

**Author**: `spec_miner_survey_accounts_2`  
**Date**: 2026-09-29  
**Target Module**: Accounts & Financial Management Module (R1–R6)  
**Codebase**: GTMS (Granite/Mining Tracking Management System — Tamil Nadu Statutory Mining ERP, Laravel 12)  
**Authoritative Request**: `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\ORIGINAL_REQUEST.md` (Header `## 2026-09-29T05:33:01Z`)  

---

## 1. Executive Summary & Specification Scope

The GTMS platform manages end-to-end statutory workflows for mining leases across Tamil Nadu. While individual statutory modules (`lease_applications`, `mining_applications`, `environment_projects`, `ppt_applications`, `dgps_surveys`, `drone_surveys`, `ec_certificates`, `ec_compliances`) have isolated registration-stage payment recording columns (`product_value`, `paid_amount`, `pending_amount`, `payment_status`) and mirror records in `application_payments`, there is currently **no unified Accounts module**.

This survey provides the authoritative blueprint for building the Accounts & Financial Management Module, covering:
1. **R1**: Dynamic Quotation Generation Engine with service line items, concession metadata auto-population, and A4 print layout.
2. **R2**: Centralized Payment Collection Engine linking all 7 statutory modules with atomic database synchronization across application tables and polymorphic `application_payments`.
3. **R3**: Official Money Receipt Voucher generation (e.g., `GTMS/REC/2026/001`) with printable A4/A5 voucher layout.
4. **R4**: Customer Financial Ledger & Statement of Account calculating chronological debits/credits and live running balances.
5. **R5**: Financial Transaction Reports with multi-parametric filtering and dependency-free UTF-8 CSV export.
6. **R6**: Sidebar UI integration and Spatie RBAC permission gating (`account.view`, `account.create`, `account.edit`, `account.delete`).

---

## 2. Route Architecture & Controller Conventions in `routes/web.php`

### 2.1 Current Routing Architecture
Inspection of `routes/web.php` (351 lines) establishes the following architectural standards:
1. **Authentication Wrapping**: All authenticated routes are enclosed within a single global middleware group:
   ```php
   Route::middleware('auth')->group(function () {
       // ... module routes ...
   });
   ```
2. **Spatie Permission Middleware**: Access control is enforced using Spatie's `permission:<permission_name>` middleware. Two dominant patterns exist:
   - **Grouped by Permission**:
     ```php
     Route::middleware('permission:dgps.view')->group(function () {
         Route::get('/dgps-survey', [DgpsSurveyController::class, 'index'])->name('dgps-survey.index');
         Route::get('/dgps-survey/{id}', [DgpsSurveyController::class, 'show'])->whereNumber('id')->name('dgps-survey.show');
     });
     ```
   - **Inline Method Chaining**:
     ```php
     Route::post('/customeradd', [CustomerDirectoryController::class, 'store'])
         ->name('customeradd')
         ->middleware('permission:customer.create');
     ```
3. **URL & Naming Conventions**:
   - Modern statutory modules employ kebab-case URLs and dot-separated route names:
     - `/ec-certificate`, `/ec-certificate/{id}`, `/ec-certificate/step/{step}` (`ec-certificate.index`, `ec-certificate.show`, `ec-certificate.store`)
     - `/ppt-department`, `/ppt-department/{id}` (`ppt-department.index`, `ppt-department.show`)
     - `/dgps-survey`, `/dgps-survey/{id}` (`dgps-survey.index`, `dgps-survey.show`)
     - `/drone-survey`, `/drone-survey/{id}` (`drone-survey.index`, `drone-survey.show`)
     - `/customer-tracking`, `/customer-tracking/{customer}` (`customer-tracking.index`, `customer-tracking.show`)
   - Parameter constraints: Numeric IDs strictly use `->whereNumber('id')` (e.g., `routes/web.php:228`, `282`, `296`).
   - Legacy routes (e.g., `customeradd`, `customeredit`, `roleadd`, `branchedit`) use flat POST endpoints without RESTful verb spoofing.

### 2.2 Recommended Route Design for Accounts Module
To adhere to modern GTMS conventions and avoid namespace collisions, the Accounts module should be mounted under a dedicated route group with prefix `accounts` and name prefix `accounts.`:

```php
// =========================================================================
// Accounts & Financial Management Module Routes
// =========================================================================
Route::middleware('auth')->prefix('accounts')->name('accounts.')->group(function () {

    // --- Quotations (R1) ---
    Route::middleware('permission:account.view')->group(function () {
        Route::get('/quotations', [QuotationController::class, 'index'])->name('quotations.index');
        Route::get('/quotations/{id}', [QuotationController::class, 'show'])->whereNumber('id')->name('quotations.show');
        Route::get('/quotations/{id}/print', [QuotationController::class, 'print'])->whereNumber('id')->name('quotations.print');
    });
    Route::middleware('permission:account.create')->group(function () {
        Route::get('/quotations/create', [QuotationController::class, 'create'])->name('quotations.create');
        Route::post('/quotations', [QuotationController::class, 'store'])->name('quotations.store');
    });
    Route::middleware('permission:account.edit')->group(function () {
        Route::get('/quotations/{id}/edit', [QuotationController::class, 'edit'])->whereNumber('id')->name('quotations.edit');
        Route::post('/quotations/{id}', [QuotationController::class, 'update'])->whereNumber('id')->name('quotations.update');
        Route::post('/quotations/{id}/status', [QuotationController::class, 'updateStatus'])->whereNumber('id')->name('quotations.status');
    });
    Route::middleware('permission:account.delete')->group(function () {
        Route::post('/quotations/{id}/delete', [QuotationController::class, 'destroy'])->whereNumber('id')->name('quotations.destroy');
    });

    // --- Payment Collection & Receipts (R2 & R3) ---
    Route::middleware('permission:account.view')->group(function () {
        Route::get('/payments', [PaymentCollectionController::class, 'index'])->name('payments.index');
        Route::get('/receipts/{id}', [PaymentCollectionController::class, 'receiptShow'])->whereNumber('id')->name('receipts.show');
        Route::get('/receipts/{id}/print', [PaymentCollectionController::class, 'receiptPrint'])->whereNumber('id')->name('receipts.print');
    });
    Route::middleware('permission:account.create')->group(function () {
        Route::get('/payments/create', [PaymentCollectionController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentCollectionController::class, 'store'])->name('payments.store');
    });

    // --- Customer Financial Ledger (R4) ---
    Route::middleware('permission:account.view')->group(function () {
        Route::get('/ledger', [CustomerLedgerController::class, 'index'])->name('ledger.index');
        Route::get('/ledger/{customerId}', [CustomerLedgerController::class, 'show'])->whereNumber('customerId')->name('ledger.show');
        Route::get('/ledger/{customerId}/statement', [CustomerLedgerController::class, 'statement'])->whereNumber('customerId')->name('ledger.statement');
        Route::get('/ledger/{customerId}/print', [CustomerLedgerController::class, 'printStatement'])->whereNumber('customerId')->name('ledger.print');
    });

    // --- Reports & CSV Export (R5) ---
    Route::middleware('permission:account.view')->group(function () {
        Route::get('/reports', [AccountReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [AccountReportController::class, 'exportCsv'])->name('reports.export');
    });

    // --- Dynamic AJAX APIs for Client/Concession & Pending Dues ---
    Route::middleware('permission:account.view')->prefix('api')->name('api.')->group(function () {
        Route::get('/customers/{id}/concessions', [PaymentCollectionController::class, 'getCustomerConcessions'])->whereNumber('id')->name('customer.concessions');
        Route::get('/customers/{id}/pending-dues', [PaymentCollectionController::class, 'getCustomerPendingDues'])->whereNumber('id')->name('customer.pending-dues');
    });
});
```

---

## 3. Existing Payment Handling & Atomic Database Transactions

### 3.1 Code Audit of Payment Recording Across Controllers
Investigation of controllers reveals how payments are recorded during initial module wizards:

1. **`CustomerController.php` (Lines 762–785 & 1212–1233)**:
   - Updates `LeaseApplication` columns: `product_value`, `paid_amount`, `pending_amount`, `payment_status`.
   - Calls `ApplicationPayment::updateOrCreate`:
     ```php
     ApplicationPayment::updateOrCreate(
         [
             'application_type' => 'lease',
             'application_id'   => $draft['application_id'],
         ],
         [
             'payable_type'   => LeaseApplication::class,
             'payable_id'     => $draft['application_id'],
             'product_value'  => $productVal,
             'paid_amount'    => $paidVal,
             'pending_amount' => $pendingVal,
             'payment_status' => $status,
         ]
     );
     ```

2. **`MiningController.php` (Lines 350–372)**:
   - Updates `MiningApplication` payment attributes.
   - Synchronizes `ApplicationPayment::updateOrCreate` with `'application_type' => 'mining'`.

3. **`EnverionsoneController.php` (Lines 214–224)**:
   - Calls `ApplicationPayment::create` with `'application_type' => 'environment'` and `'payable_type' => EnvironmentProject::class`.

4. **`PptDepartmentController.php` (Lines 191–246)**:
   - Uses explicit transaction boundary:
     ```php
     DB::beginTransaction();
     try {
         $ppt = PptApplication::create([...]);
         ApplicationPayment::create([
             'application_type' => 'ppt',
             'application_id'   => $ppt->id,
             'product_value'    => $val,
             'paid_amount'      => $paid,
             'pending_amount'   => $pending,
             'payment_status'   => $pStatus,
             'notes'            => '...',
         ]);
         DB::commit();
     } catch (\Exception $e) {
         DB::rollBack();
     }
     ```

5. **`DgpsSurveyController.php` (Lines 235–246)**:
   - Wraps survey creation and `ApplicationPayment::create` (`'application_type' => 'dgps'`) inside `DB::beginTransaction()` and `DB::commit()`.

6. **`DroneSurveyController.php` (Lines 205–218)**:
   - Wraps survey creation and `ApplicationPayment::create` (`'application_type' => 'drone'`) inside `DB::beginTransaction()` and `DB::commit()`.

7. **`EcCertificateController.php` (Lines 475–486)**:
   - Creates `ApplicationPayment` with `'application_type' => 'ec'` and `'payable_type' => EcCertificate::class`.

8. **`EcComplianceController.php` (Lines 335–344)**:
   - Creates `ApplicationPayment` with `'application_type' => 'ec_compliance'`.

### 3.2 Polymorphic Structure of `ApplicationPayment`
Defined in `app/Models/ApplicationPayment.php` and migration `database/migrations/2026_09_22_000002_add_payment_fields_to_applications_tables.php`:
- `application_type`: String (50) — `'lease'`, `'mining'`, `'environment'`, `'ppt'`, `'dgps'`, `'drone'`, `'ec'`, `'ec_compliance'`.
- `application_id`: Unsigned BigInteger.
- `payable_type` & `payable_id`: Standard Laravel polymorphic columns (`$table->nullableMorphs('payable')`).
- `product_value`, `paid_amount`, `pending_amount`: Decimal(12, 2).
- `payment_status`: Enum(`'pending'`, `'partial'`, `'paid'`).
- `notes`: Text, nullable.

**Key Finding**: `ApplicationPayment` represents the **current aggregate financial state** of an application. It does **not** store an audit trail of individual installment transactions (payment mode, cheque number, bank name, payment date, receipt voucher numbers).  
Therefore, R2 and R3 require an explicit `receipt_vouchers` (or `payment_collections`) table to log each transaction event, and atomically recalculate and update both the application record and `ApplicationPayment`.

### 3.3 Atomic Synchronization Flow for Payment Collection (R2)
When an accountant collects a payment of amount $A$ against application $(T, ID)$:
```
  [Payment Collection Form Submitted]
                 │
                 ▼
       DB::beginTransaction()
                 │
     ┌───────────┴───────────────────────────────┐
     ▼                                           ▼
1. Fetch target Application record          2. Compute updated balances:
   (e.g., MiningApplication::lockForUpdate)     new_paid = current_paid + A
                                                new_pending = max(0, product_value - new_paid)
                                                new_status = (new_pending <= 0) ? 'paid' : 'partial'
     ┌───────────────────────────────────────────┘
     ▼
3. Update Application Record:
   UPDATE mining_applications
   SET paid_amount = new_paid, pending_amount = new_pending, payment_status = new_status
     │
     ▼
4. Atomically sync ApplicationPayment aggregate record:
   ApplicationPayment::updateOrCreate(
       ['application_type' => $type, 'application_id' => $id],
       [
           'payable_type'   => get_class($app),
           'payable_id'     => $id,
           'product_value'  => $app->product_value,
           'paid_amount'    => $new_paid,
           'pending_amount' => $new_pending,
           'payment_status' => $new_status,
           'notes'          => "Receipt Voucher: {$voucherNo}"
       ]
   );
     │
     ▼
5. Insert Immutable Receipt Voucher Record:
   ReceiptVoucher::create([
       'voucher_no'       => $voucherNo,
       'customer_id'      => $customerId,
       'application_type' => $type,
       'application_id'   => $id,
       'amount_paid'      => $A,
       'payment_mode'     => $paymentMode, // Cash, Cheque, NEFT/RTGS, UPI/GPay
       'reference_no'     => $bankRef,
       'payment_date'     => $date,
       'received_by'      => Auth::id(),
   ]);
     │
     ▼
       DB::commit() ──> [Redirect to Receipt Print / Voucher View]
```

---

## 4. Spatie RBAC Setup & Accounts Permissions

### 4.1 RBAC Architecture Audit
Inspection of `database/seeders/RolePermissionSeeder.php` (lines 20–280) and `app/Models/User.php`:
1. **Permission Naming Standard**: Permissions consistently use `<module>.<action>` lowercase dot-notation:
   - `customer.view`, `customer.create`, `customer.edit`, `customer.delete`
   - `branch.view`, `branch.create`, `branch.edit`, `branch.delete`
   - `mining.view`, `mining.create`, `mining.edit`, `mining.delete`
   - `dgps.view`, `dgps.create`, `dgps.edit`, `dgps.delete`
2. **Default Roles**:
   - `Admin`: Super-administrator role. Assigned all permissions via `$adminRole->syncPermissions(Permission::all());` (line 133). In addition, `BranchScope` bypasses filtering for users with `hasRole(['Admin', 'Super Admin'])`.
   - `Staff`: Operational read-only or limited staff. Currently assigned `.view` permissions.
   - `Officer`: Field & administrative officers. Assigned `.view`, `.create`, `.edit`, and `.manage` permissions.
3. **Guard**: Guard is uniformly `'web'`.

### 4.2 Required Accounts Permissions
To seamlessly integrate with GTMS RBAC, the following 4 permissions must be added to `RolePermissionSeeder.php`:

| Permission Name | Description | Admin | Officer | Staff |
|---|---|:---:|:---:|:---:|
| `account.view` | View Quotations list, Payment history, Receipt Vouchers, Customer Ledgers, and Financial Reports | Yes | Yes | Yes |
| `account.create` | Generate new Quotations and execute Payment Collections / Receipt generation | Yes | Yes | No |
| `account.edit` | Modify draft Quotations, update quotation status, and amend collection notes | Yes | Yes | No |
| `account.delete` | Cancel/void draft Quotations or purge invalid entries | Yes | No | No |

### 4.3 Permission Enforcement Patterns
1. **Route Level**: Enforced via `middleware('permission:account.view')` etc., as shown in Section 2.2.
2. **Controller Level**: In `__construct()` or action methods:
   ```php
   $this->middleware('permission:account.view')->only(['index', 'show', 'print']);
   $this->middleware('permission:account.create')->only(['create', 'store']);
   $this->middleware('permission:account.edit')->only(['edit', 'update']);
   $this->middleware('permission:account.delete')->only(['destroy']);
   ```
3. **Blade UI Level**:
   ```blade
   @can('account.create')
       <a href="{{ route('accounts.quotations.create') }}" class="btn btn-primary">New Quotation</a>
   @endcan
   ```

---

## 5. Survey of Statutory Module Models & Customer Pending Dues Resolver

### 5.1 The 7 Statutory Modules Schema & Field Matrix
Each statutory module is linked to `Customer` (`customer_id`) and stores financial and concession details:

| # | Statutory Module | Model Class | DB Table | Ref Column | Concession Location Columns | Financial Columns Present |
|---|---|---|---|---|---|---|
| 1 | Lease Application | `App\Models\LeaseApplication` | `lease_applications` | `application_no` / `common_id` | `taluk`, `village`, `district_id`, `area_extent_ha`, `surveyNumbers()` relation | `product_value`, `paid_amount`, `pending_amount`, `payment_status` |
| 2 | Mining Plan | `App\Models\MiningApplication` | `mining_applications` | `application_no` / `common_id` | `taluk`, `village`, `district_id`, `survey_numbers_text`, `area_extent_ha` | `product_value`, `paid_amount`, `pending_amount`, `payment_status` |
| 3 | Environment Clearance | `App\Models\EnvironmentProject` | `environment_projects` | `project_code` / `project_name` | `location`, `district_id`, `category`, `sub_category` | `product_value`, `paid_amount`, `pending_amount`, `payment_status` |
| 4 | PPT Department | `App\Models\PptApplication` | `ppt_applications` | `application_no` | `taluk_village`, `district_id` | `product_value`, `paid_amount`, `pending_amount`, `payment_status` |
| 5 | DGPS Survey | `App\Models\DgpsSurvey` | `dgps_surveys` | `survey_no` | `location`, `surveyed_area_ha`, `lease_area_ha` | `product_value`, `paid_amount`, `pending_amount`, `payment_status` |
| 6 | Drone Survey | `App\Models\DroneSurvey` | `drone_surveys` | `survey_no` | `location`, `lease_area` | `product_value`, `paid_amount`, `pending_amount`, `payment_status` |
| 7 | EC Certificate | `App\Models\EcCertificate` | `ec_certificates` | `ec_ref_no` / `parivesh_app_no` | Inherits from `environmentProject` | `product_value`, `paid_amount`, `pending_amount`, `payment_status` |
| + | EC Compliance | `App\Models\EcCompliance` | `ec_compliances` | `compliance_no` | `project_name`, `district_id` | `product_value`, `paid_amount`, `pending_amount`, `payment_status` |

### 5.2 Customer Pending Dues Resolver Algorithm
To satisfy requirement R2 ("Query and display all outstanding dues across all 7 statutory modules"), the controller implements a unified resolver:

```php
public function getCustomerPendingDues(int $customerId)
{
    $customer = Customer::with('district')->findOrFail($customerId);

    $dues = collect();

    // 1. Lease Applications
    $customer->leaseApplications()
        ->where('pending_amount', '>', 0)
        ->get()
        ->each(function ($item) use ($dues) {
            $dues->push([
                'module_type'     => 'lease',
                'module_label'    => 'Mining Lease Application',
                'application_id'  => $item->id,
                'reference_no'    => $item->application_no ?: $item->common_id ?: "LEASE-{$item->id}",
                'concession_info' => "Taluk: {$item->taluk}, Village: {$item->village}",
                'product_value'   => (float)$item->product_value,
                'paid_amount'     => (float)$item->paid_amount,
                'pending_amount'  => (float)$item->pending_amount,
                'payment_status'  => $item->payment_status,
            ]);
        });

    // 2. Mining Plan Applications
    $customer->miningApplications()
        ->where('pending_amount', '>', 0)
        ->get()
        ->each(function ($item) use ($dues) {
            $dues->push([
                'module_type'     => 'mining',
                'module_label'    => 'Mining Plan Preparation',
                'application_id'  => $item->id,
                'reference_no'    => $item->application_no ?: $item->common_id ?: "MP-{$item->id}",
                'concession_info' => "S.F. {$item->survey_numbers_text}, {$item->village}",
                'product_value'   => (float)$item->product_value,
                'paid_amount'     => (float)$item->paid_amount,
                'pending_amount'  => (float)$item->pending_amount,
                'payment_status'  => $item->payment_status,
            ]);
        });

    // 3. Environment Projects
    $customer->environmentProjects()
        ->where('pending_amount', '>', 0)
        ->get()
        ->each(function ($item) use ($dues) {
            $dues->push([
                'module_type'     => 'environment',
                'module_label'    => "EC Project ({$item->category})",
                'application_id'  => $item->id,
                'reference_no'    => $item->project_code ?: $item->project_name,
                'concession_info' => $item->location,
                'product_value'   => (float)$item->product_value,
                'paid_amount'     => (float)$item->paid_amount,
                'pending_amount'  => (float)$item->pending_amount,
                'payment_status'  => $item->payment_status,
            ]);
        });

    // 4. PPT Applications
    $customer->pptApplications()
        ->where('pending_amount', '>', 0)
        ->get()
        ->each(function ($item) use ($dues) {
            $dues->push([
                'module_type'     => 'ppt',
                'module_label'    => 'PPT Presentation / SEAC Appraisal',
                'application_id'  => $item->id,
                'reference_no'    => $item->application_no ?: "PPT-{$item->id}",
                'concession_info' => $item->taluk_village,
                'product_value'   => (float)$item->product_value,
                'paid_amount'     => (float)$item->paid_amount,
                'pending_amount'  => (float)$item->pending_amount,
                'payment_status'  => $item->payment_status,
            ]);
        });

    // 5. DGPS Surveys
    $customer->dgpsSurveys()
        ->where('pending_amount', '>', 0)
        ->get()
        ->each(function ($item) use ($dues) {
            $dues->push([
                'module_type'     => 'dgps',
                'module_label'    => 'DGPS Demarcation Survey',
                'application_id'  => $item->id,
                'reference_no'    => $item->survey_no ?: "DGPS-{$item->id}",
                'concession_info' => $item->location,
                'product_value'   => (float)$item->product_value,
                'paid_amount'     => (float)$item->paid_amount,
                'pending_amount'  => (float)$item->pending_amount,
                'payment_status'  => $item->payment_status,
            ]);
        });

    // 6. Drone Surveys
    $customer->droneSurveys()
        ->where('pending_amount', '>', 0)
        ->get()
        ->each(function ($item) use ($dues) {
            $dues->push([
                'module_type'     => 'drone',
                'module_label'    => 'Drone Aerial Survey',
                'application_id'  => $item->id,
                'reference_no'    => $item->survey_no ?: "DRONE-{$item->id}",
                'concession_info' => $item->location,
                'product_value'   => (float)$item->product_value,
                'paid_amount'     => (float)$item->paid_amount,
                'pending_amount'  => (float)$item->pending_amount,
                'payment_status'  => $item->payment_status,
            ]);
        });

    // 7. EC Certificates
    $customer->ecCertificates()
        ->where('pending_amount', '>', 0)
        ->get()
        ->each(function ($item) use ($dues) {
            $dues->push([
                'module_type'     => 'ec',
                'module_label'    => 'EC Certificate Issuance',
                'application_id'  => $item->id,
                'reference_no'    => $item->ec_ref_no ?: $item->parivesh_app_no ?: "EC-{$item->id}",
                'concession_info' => $item->applicant_name,
                'product_value'   => (float)$item->product_value,
                'paid_amount'     => (float)$item->paid_amount,
                'pending_amount'  => (float)$item->pending_amount,
                'payment_status'  => $item->payment_status,
            ]);
        });

    // Supplementary: EC Compliance
    $customer->ecCompliances()
        ->where('pending_amount', '>', 0)
        ->get()
        ->each(function ($item) use ($dues) {
            $dues->push([
                'module_type'     => 'ec_compliance',
                'module_label'    => 'Half-Yearly EC Compliance',
                'application_id'  => $item->id,
                'reference_no'    => $item->compliance_no ?: "COMP-{$item->id}",
                'concession_info' => $item->project_name,
                'product_value'   => (float)$item->product_value,
                'paid_amount'     => (float)$item->paid_amount,
                'pending_amount'  => (float)$item->pending_amount,
                'payment_status'  => $item->payment_status,
            ]);
        });

    return response()->json([
        'status' => 1,
        'customer' => [
            'id'            => $customer->id,
            'name'          => $customer->company_name ?: $customer->customer_name,
            'mobile'        => $customer->mobile_num,
            'gstin'         => $customer->gstin,
            'district_name' => $customer->district?->name,
        ],
        'dues' => $dues,
        'total_pending' => $dues->sum('pending_amount')
    ]);
}
```

### 5.3 Concession Metadata Auto-Population for Quotations (R1)
When creating a quotation, selecting a customer triggers `getCustomerConcessions(int $customerId)` which collates active quarry concession details from existing `MiningApplication` or `LeaseApplication` records:
- **Village & Taluk**: Extracted from `mining_applications.village` or `lease_applications.village`.
- **District**: Linked district name.
- **Survey Numbers**: From `mining_applications.survey_numbers_text` or `lease_survey_numbers` table.
- **Extent**: Total area in hectares (`area_extent_ha`).
- **Mineral**: Associated mineral name (e.g., "Rough Stone", "Gravel", "Black Granite").

---

## 6. Functional Specifications for Requirements R1–R6

### 6.1 R1: Quotation Generation Engine & High-Fidelity Print Layout
- **Quotation Number Generator**: Sequential numbering format: `GTMS/QTN/YYYY/XXXX` (e.g., `GTMS/QTN/2026/0001`). Generated using:
  ```php
  $year = date('Y');
  $seq = Quotation::whereYear('created_at', $year)->count() + 1;
  $quotationNo = sprintf('GTMS/QTN/%s/%04d', $year, $seq);
  ```
- **Service Catalog**:
  1. DGPS Boundary Survey & Demarcation (SAC: 998341) — Unit rate per Ha / Pillar.
  2. Drone Aerial Volumetric Survey & 3D Modeling (SAC: 998342) — Rate per Ha / Grid flight.
  3. Mining Plan & PMCP Preparation (SAC: 998343) — Lump sum / based on production capacity.
  4. Environmental Clearance (Form-1, Form-2, PFR & Baseline EMP) (SAC: 998349) — Category B1 / B2 rates.
  5. TNPCB CTE/CTO (Consent to Establish / Operate) (SAC: 998349) — Statutory documentation.
  6. Half-Yearly Environmental Compliance & Environmental Monitoring (SAC: 998349) — Per semester.
- **Tax Calculation**:
  - Subtotal = $\sum (\text{Line Item Amounts})$
  - CGST @ 9% = $\text{round}(\text{Subtotal} \times 0.09, 2)$
  - SGST @ 9% = $\text{round}(\text{Subtotal} \times 0.09, 2)$
  - Grand Total = $\text{Subtotal} + \text{CGST} + \text{SGST}$
- **Number to Words**: Converted using Indian numbering hierarchy (`numberToWords()` implementation in `CustomerTrackingController.php:1642–1701` supporting Crores, Lakhs, Thousands, Hundreds, Rupees, and Paise).
- **A4 Print View**:
  - Reuses the battle-tested `.page-sheet` CSS and `.no-print-bar` from `resources/views/pages/customer_tracking/proforma_invoice.blade.php`.
  - GTMS insignia header, quotation metadata box, tabular item breakdown, terms & conditions block, bank NEFT/RTGS mandate details, and authorized signatory signature line.

### 6.2 R2: Centralized Payment Collection Engine
- **Targeting**: Can collect against any specific statutory application across the 7 modules or general on-account deposit.
- **Form Controls**:
  - Customer Selector (with Live Select2 autocomplete).
  - Outstanding Dues Table (dynamically populated via AJAX `getCustomerPendingDues`).
  - Radio/Selection for target application.
  - Payment Details:
    - `amount_paid`: Numeric, $> 0$, must not exceed pending amount unless marked as advance.
    - `payment_mode`: Select (`Cash`, `Cheque`, `NEFT/RTGS`, `UPI/GPay`).
    - `reference_no`: Text (Cheque No, UTR Number, UPI Transaction ID).
    - `bank_name`: Bank branch if cheque/NEFT.
    - `payment_date`: Date (defaults to today).
    - `notes`: Optional narrative.
- **Transaction Safety**: Atomic lock on the target application row, simultaneous update of application table, `application_payments`, and insertion of `receipt_vouchers` record inside `DB::transaction(...)`.

### 6.3 R3: Official Money Receipt Voucher Generation & Printing
- **Voucher Number Generator**: Sequential numbering format: `GTMS/REC/YYYY/XXXX` (e.g., `GTMS/REC/2026/0001`).
- **Data Payload**:
  - Receipt Voucher No, Date, Time.
  - Received From: Customer Name, Company Name, Address, GSTIN, Mobile.
  - On Account Of: Target Application Title, Reference Number (e.g. `Mining Plan Application MP-2026-004`), Survey Number / Location (`S.F. 102/1A, Melur Village`).
  - Total Billed: ₹ X, Amount Now Paid: ₹ Y, Balance Remaining: ₹ Z.
  - Payment Method: Cheque / NEFT / UPI with Reference ID and Bank.
  - Amount in words: Converted to Indian currency text.
  - Collecting Officer: Name, User Code, and Branch.
- **Print Layout**: Clean A4 / A5 half-page receipt voucher layout with official GTMS border, stamp block, and one-click print button.

### 6.4 R4: Customer Financial Ledger & Statement of Account
- **Ledger Model**: Single-pane-of-glass chronological statement for any quarry owner.
- **Data Composition**:
  - **Debits (+)**: Billed Quotations (or initial registered application product values).
  - **Credits (-)**: Collected payments and receipt vouchers.
- **Running Balance**: Computed row by row in chronological order:
  $$\text{Running Balance}_i = \text{Running Balance}_{i-1} + \text{Debit}_i - \text{Credit}_i$$
- **Summary Header**:
  - Total Invoiced / Billed (₹)
  - Total Paid / Realized (₹)
  - Net Outstanding Balance Due (₹)
- **Output Views**: Interactive DataTables view with date filtering + Printable Statement of Account view.

### 6.5 R5: Financial Reports & Export
- **KPI Summary Cards**:
  1. *Total Collected (All-time)*: Total realized revenue.
  2. *Total Collected (Month-to-date)*: Current calendar month collections.
  3. *Total Outstanding Receivables*: Sum of `pending_amount` across active statutory modules.
  4. *Active Quotations Value*: Total value of pending quotations.
- **Filter Parameters**:
  - `date_from` & `date_to`: Date range.
  - `customer_id`: Filter by specific quarry owner.
  - `module_type`: Filter by statutory module (`mining`, `dgps`, `drone`, `environment`, `ppt`, `lease`, `ec`).
  - `payment_mode`: Filter by payment mode (`Cash`, `Cheque`, `NEFT/RTGS`, `UPI/GPay`).
- **CSV Export Architecture**:
  - Uses native PHP `response()->streamDownload(...)` with `fputcsv()`.
  - Requires **zero external Composer packages**.
  - Automatically prepends UTF-8 BOM (`\xEF\xBB\xBF`) to guarantee flawless opening in Windows Microsoft Excel without encoding corruption.

### 6.6 R6: UI Integration & Sidebar
- **Sidebar Integration**: Added directly to `resources/views/layouts/sidebar.blade.php` right after Drone Survey (Line 115):
  ```blade
  @can('account.view')
  <li>
      <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
          <i class="fas fa-file-invoice-dollar"></i>
          <span class="nav-text">Accounts</span>
      </a>
      <ul aria-expanded="false">
          <li><a href="{{ route('accounts.quotations.index') }}">Quotations</a></li>
          <li><a href="{{ route('accounts.payments.create') }}">Collect Payment</a></li>
          <li><a href="{{ route('accounts.ledger.index') }}">Customer Ledger</a></li>
          <li><a href="{{ route('accounts.reports.index') }}">Reports</a></li>
      </ul>
  </li>
  @endcan
  ```
- **Styling**: Aligns with GTMS MetisMenu, Bootstrap 5 cards, Lucide/FontAwesome 5 icons, and Datatables styling tokens.

---

## 7. Mandatory Specification Miner Tables

### Features Discovered
| # | Category | Feature | Description | Inputs | Outputs | Error Behavior | Discovered Via |
|---|---|---|---|---|---|---|---|
| 1 | RBAC | Spatie Permission Registrations | Role and permission definitions for Accounts module | Permission strings (`account.view`, `account.create`, `account.edit`, `account.delete`) | Spatie `Permission` and `Role` model records | Throws `PermissionAlreadyExists` if duplicated | `database/seeders/RolePermissionSeeder.php:25–126` |
| 2 | Routing | Route Namespace & Middleware Gating | Centralized `/accounts` URL hierarchy protected by `auth` & `permission:*` | HTTP Verbs (GET, POST), URLs | HTTP Response (View or JSON) | 403 Forbidden on missing permission, 401 on unauthenticated | `routes/web.php:34–349` |
| 3 | Accounts / Quotation | Quotation Generation Engine | Dynamic quotation creation with service lines, CGST/SGST (18%), and validity | `customer_id`, `items` (array with `service_name`, `sac`, `rate`, `qty`), `validity_days` | Created `quotations` & `quotation_items` records | 422 Validation error on empty line items or missing customer | `ORIGINAL_REQUEST.md:141–147` |
| 4 | Accounts / Quotation | Quotation A4 Print & PDF View | High-fidelity A4 printable quotation with GTMS insignia, Indian numbering text, and signature block | `quotation_id` | Rendered HTML print sheet with `@media print` CSS | 404 Not Found if quotation does not exist | `resources/views/pages/customer_tracking/proforma_invoice.blade.php` |
| 5 | Accounts / Payment | Customer Pending Dues Resolver | AJAX endpoint aggregating outstanding balances across all 7 statutory modules | `customer_id` (via URL) | JSON array of pending dues (`module_type`, `application_id`, `product_value`, `paid_amount`, `pending_amount`) | 404 Not Found if customer not found | `CustomerTrackingController.php:64–75`, `app/Models/Customer.php:99–142` |
| 6 | Accounts / Payment | Customer Concession Metadata Resolver | AJAX endpoint auto-populating village, taluk, district, survey numbers, and extent | `customer_id` (via URL) | JSON object of concession metadata | 404 Not Found if customer not found | `app/Models/LeaseApplication.php:18–50`, `app/Models/MiningApplication.php:18–52` |
| 7 | Accounts / Payment | Centralized Payment Collection & Atomic Sync | Collect partial/full payment and atomically synchronize target application table and `application_payments` | `customer_id`, `application_type`, `application_id`, `amount_paid`, `payment_mode`, `reference_no`, `payment_date` | Created `receipt_vouchers` record, updated target table and `application_payments` | DB rollback on exception, 422 if amount > pending | `PptDepartmentController.php:191–246`, `MiningController.php:350–372` |
| 8 | Accounts / Receipt | Receipt Voucher Generation & Printing | Instant generation and formatted A4/A5 voucher printing for collected payment | `voucher_id` | Rendered printable receipt voucher HTML | 404 Not Found if voucher ID invalid | `ORIGINAL_REQUEST.md:154–159` |
| 9 | Accounts / Ledger | Customer Financial Dossier & Running Ledger | Unified chronological statement of account showing debits, credits, and live running balance | `customer_id`, optional date range | Ledger view with KPI cards (total billed, received, balance) | 404 Not Found if customer invalid | `ORIGINAL_REQUEST.md:160–165` |
| 10 | Accounts / Reports | Multi-parametric Financial Transaction Log | Searchable financial transaction reports filterable by date, customer, module, and mode | `date_from`, `date_to`, `customer_id`, `module_type`, `payment_mode` | Rendered report table with summary KPIs | Gracefully handles empty results | `CustomerTrackingController.php:23–170` |
| 11 | Accounts / Reports | Dependency-Free CSV Export | Streamed CSV download of financial records with UTF-8 BOM encoding for Excel compatibility | Filter parameters (same as report) | Streamed file download (`gtms_financial_report_YYYY-MM-DD.csv`) | 500 error if stream fails | `composer.json:8–13` (Zero third-party Excel package requirement) |
| 12 | Navigation | Unified Accounts Sidebar Integration | Sidebar accordion with links to Quotations, Collect Payment, Customer Ledger, Reports | Authenticated User Spatie Permissions | Sidebar MetisMenu HTML component | Hidden if user lacks `account.view` | `resources/views/layouts/sidebar.blade.php:115–120` |

### Edge Cases
| # | Feature | Input | Observed Behavior |
|---|---|---|---|
| 1 | Payment Collection | Payment amount entered exceeds application `pending_amount` | Must be blocked by backend validation: `'amount_paid' => ['required', 'numeric', 'min:1', 'max:' . $targetApp->pending_amount]` unless explicitly flagged as advance. |
| 2 | Payment Collection | Customer has multiple applications across modules with identical survey numbers | Resolver uniquely disambiguates each due item by `module_type` and `application_id`, displaying the specific module label (e.g., "Mining Plan (MP-2026-001)" vs "DGPS Survey (DGPS-2026-003)"). |
| 3 | Payment Collection | Concurrent payment submissions for the same application | Mitigated by using database row-level locking (`lockForUpdate()`) and wrapping calculations within `DB::transaction(...)`. |
| 4 | Quotation Print | Customer has no registered company name (only individual proprietor name) | Null-safe fallback: `$customer->company_name ?: $customer->customer_name`. Address falls back to "Address on file" if null. |
| 5 | Quotation Print | Quotation total contains decimal Paise (e.g. ₹ 1,45,250.50) | `numberToWords()` converts both whole rupees and paise: e.g., "One Lakh Forty Five Thousand Two Hundred Fifty Rupees and Fifty Paise Only". |
| 6 | Customer Ledger | Customer has zero applications and zero payments recorded | Displays empty state: Total Billed: ₹ 0.00, Total Paid: ₹ 0.00, Outstanding: ₹ 0.00 with helpful "No financial transactions recorded for this customer yet." message. |
| 7 | CSV Export | Customer company name or service description contains commas or quotation marks | `fputcsv()` automatically encloses fields in double quotes and escapes internal quotes, preventing CSV injection and column misalignment. |
| 8 | RBAC Gating | User with `Staff` role attempts to access `/accounts/quotations/create` or `/accounts/payments/create` | Spatie `permission:account.create` middleware automatically intercepts request and returns HTTP 403 Forbidden. |
| 9 | Soft Deletes | Customer is soft-deleted after quotations or receipts are issued | Relationships on `Quotation` and `ReceiptVoucher` must use `->belongsTo(Customer::class)->withTrashed()` to prevent 500 exceptions on historical records. |

---

## 8. Database Schema Blueprint for Accounts Module

To support the controllers without altering any existing statutory tables, two dedicated migrations are required:

### 8.1 Migration 1: Quotations & Quotation Line Items
```php
Schema::create('quotations', function (Blueprint $table) {
    $table->id();
    $table->string('quotation_no', 50)->unique(); // e.g. GTMS/QTN/2026/0001
    $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
    $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
    $table->string('village', 100)->nullable();
    $table->string('taluk', 100)->nullable();
    $table->string('survey_numbers', 255)->nullable();
    $table->decimal('area_extent_ha', 10, 4)->nullable();
    $table->date('quotation_date');
    $table->integer('validity_days')->default(30);
    $table->decimal('subtotal', 12, 2)->default(0.00);
    $table->decimal('cgst', 12, 2)->default(0.00);
    $table->decimal('sgst', 12, 2)->default(0.00);
    $table->decimal('grand_total', 12, 2)->default(0.00);
    $table->enum('status', ['draft', 'sent', 'accepted', 'rejected', 'expired'])->default('draft');
    $table->text('payment_terms')->nullable();
    $table->text('scope_of_work')->nullable();
    $table->text('notes')->nullable();
    $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
    $table->timestamps();
    $table->softDeletes();

    $table->index(['customer_id', 'status']);
    $table->index('quotation_date');
});

Schema::create('quotation_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('quotation_id')->constrained('quotations')->cascadeOnDelete();
    $table->string('service_name', 255);
    $table->string('sac_code', 20)->nullable();
    $table->text('description')->nullable();
    $table->decimal('quantity', 10, 2)->default(1.00);
    $table->string('unit', 50)->default('Lump Sum');
    $table->decimal('unit_rate', 12, 2)->default(0.00);
    $table->decimal('amount', 12, 2)->default(0.00);
    $table->integer('sort_order')->default(1);
    $table->timestamps();
});
```

### 8.2 Migration 2: Receipt Vouchers
```php
Schema::create('receipt_vouchers', function (Blueprint $table) {
    $table->id();
    $table->string('voucher_no', 50)->unique(); // e.g. GTMS/REC/2026/0001
    $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
    $table->string('application_type', 50); // lease, mining, environment, ppt, dgps, drone, ec, ec_compliance
    $table->unsignedBigInteger('application_id');
    $table->nullableMorphs('payable'); // polymorphic link to application instance
    $table->decimal('amount_paid', 12, 2);
    $table->enum('payment_mode', ['Cash', 'Cheque', 'NEFT/RTGS', 'UPI/GPay'])->default('Cash');
    $table->string('reference_no', 100)->nullable(); // Cheque/UTR/UPI Ref
    $table->string('bank_name', 100)->nullable();
    $table->date('payment_date');
    $table->text('notes')->nullable();
    $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
    $table->timestamps();
    $table->softDeletes();

    $table->index(['customer_id', 'payment_date']);
    $table->index(['application_type', 'application_id']);
});
```

---

## 9. Conclusion & Implementation Recommendations

1. **Controller Architecture**: Implement 4 focused controllers under `app/Http/Controllers/Accounts/` (or directly in `app/Http/Controllers/` following GTMS pattern):
   - `QuotationController.php` (R1)
   - `PaymentCollectionController.php` (R2 & R3)
   - `CustomerLedgerController.php` (R4)
   - `AccountReportController.php` (R5)
2. **Atomic Synchronization**: Always execute the payment collection updates within `DB::transaction()` to ensure `target_application`, `application_payments`, and `receipt_vouchers` remain perfectly consistent.
3. **Spatie RBAC Seed**: Update `RolePermissionSeeder.php` to register `account.view`, `account.create`, `account.edit`, and `account.delete`, and assign them to `Admin` (all 4) and `Officer` (view, create, edit).
4. **Testing Suite**: Develop `tests/Feature/AccountsModuleTest.php` leveraging `DatabaseTransactions` to verify all endpoints, atomic rollbacks, calculation accuracy, and Spatie permission boundaries without mutating production data.

# GTMS Accounts & Financial Management Module — UI/UX, Layout & Styling Survey Report

**Explorer**: `explorer_survey_accounts_3`  
**Date**: 2026-09-29  
**Target Module**: Accounts & Financial Management (R1–R6)  
**Codebase Base**: Laravel 12 ERP / DexignLabs Fillow Theme / Bootstrap 5.3  
**Working Directory**: `c:\xampp\htdocs\GTMS\gtms\.agents\teamwork\explorer_survey_accounts_3`

---

## Executive Summary

This report delivers an exhaustive architectural and visual survey of the GTMS frontend presentation layer to guide the design and implementation of the **Accounts & Financial Management Module**.

Key findings:
1. **Master Layout Hierarchy**: Root layout `resources/views/layouts/app.blade.php` yields `@yield('title')`, `@yield('main_content')`, `@yield('scripts')`, and `@stack('scripts')`. Note that `<head>` lacks `@stack('styles')` or `@yield('styles')`; page-specific CSS styles are conventionally embedded inside `<style>` blocks at the top of `@section('main_content')`.
2. **Sidebar Navigation**: Defined in `resources/views/layouts/sidebar.blade.php` using MetisMenu (`<ul class="metismenu" id="menu">`). Active links are automatically detected client-side by `Fillow.handleCurrentActive()` matching `window.location == this.href`, and can be assisted server-side with `{{ request()->routeIs('accounts.*') ? 'mm-active' : '' }}`.
3. **Print Engine Architecture**: Two battle-tested print templates exist (`resources/views/pages/customer_tracking/tax_invoice.blade.php` and `proforma_invoice.blade.php`). They use standalone HTML5 documents bypassing `layouts.app` to prevent admin chrome interference, featuring a standardized screen action bar (`.no-print-bar`), exact A4/A5 CSS `@page` media rules, GTMS company corporate branding (`images/invoices/gtms_logo.png`, `gtms_pi_banner.png`), and official digital seals (`gtms_stamp.png`).
4. **Form & Data Patterns**: Standardized Bootstrap 5 forms (`.form-control`, `.form-select`, `.form-label.fw-bold.text-navy`), DataTables with unified GTMS pagination CSS (`#0F1E4D` active state), SweetAlert2 warnings/confirms (`confirmButtonColor: '#0F1E4D'`), and dynamic AJAX customer lookup via existing `/customers/lookup-mimas/{mimas_no}` endpoint.
5. **RBAC & Permissions**: Granular Spatie permissions (`account.view`, `account.create`, `account.edit`, `account.delete`) map cleanly to `RolePermissionSeeder.php` and auto-populate in the Role matrix modal via `RolesController.php` grouped module keys.

---

## 1. Master Layout Hierarchy & Asset Architecture

### 1.1 Master Layout Inspection (`resources/views/layouts/app.blade.php`)

The application layout follows a classic modular wrapper structure:

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title')</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="/images/gtmslogo.png">

    <!-- Core Vendor CSS -->
    <link href="/vendor/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="/vendor/owl-carousel/owl.carousel.css" rel="stylesheet">
    <link rel="stylesheet" href="/vendor/nouislider/nouislider.min.css">
    <link href="/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="/vendor/datatables/responsive/responsive.css" rel="stylesheet">
    <link rel="stylesheet" href="/vendor/toastr/css/toastr.min.css">
    <link href="/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="/css/style.css?v=2" rel="stylesheet">

    <!-- Global Pagination Harmonization -->
    <style>
        .pagination .page-item.active .page-link,
        .dataTables_wrapper .dataTables_paginate span .paginate_button.current {
            background-color: #0F1E4D !important;
            border-color: #0F1E4D !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body>
    <div id="preloader">...</div>
    <div id="main-wrapper">
        @include('layouts.header')
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        @yield('main_content')

        @include('layouts.footer')
    </div>

    <!-- Vendor & Theme Scripts -->
    <script src="/vendor/global/global.min.js"></script>
    <script src="/vendor/toastr/js/toastr.min.js"></script>
    <script src="/js/plugins-init/toastr-init.js"></script>
    <script src="/vendor/sweetalert2/sweetalert2.min.js"></script>
    <script src="/js/plugins-init/sweetalert.init.js"></script>
    <script src="/vendor/bootstrap-select/js/bootstrap-select.min.js"></script>
    <script src="/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="/vendor/datatables/responsive/responsive.js"></script>
    <script src="/js/plugins-init/datatables.init.js"></script>
    <script src="/js/custom.min.js"></script>
    <script src="/js/app.js?v=2"></script>
    <script src="/js/admin.js"></script>
    <script src="/js/dlabnav-init.js"></script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
```

### 1.2 Content Yields and Asset Directives

| Directive | Location in Layout | Purpose | Accounts Module Usage |
|---|---|---|---|
| `@yield('title')` | `<head><title>` (Line 6) | Browser tab title | `@section('title', 'Quotations Management')` |
| `@yield('main_content')` | `<div id="main-wrapper">` (Line 194) | Primary view markup | Wraps `.content-body > .container-fluid` |
| `<style>` in `@section('main_content')` | Top of content block | Custom page styling | Defines UI tokens and custom responsive grids |
| `@yield('scripts')` | Before `</body>` (Line 349) | Legacy script injection | Injects module JS scripts |
| `@stack('scripts')` | Before `</body>` (Line 350) | Push-based script stacks | Pushes inline `<script>` blocks |

> **Critical Architecture Rule**: Because `layouts.app` **does NOT** contain `@stack('styles')` or `@yield('styles')` in the `<head>`, all custom CSS and stylesheet links for standard web screens (e.g. `<link href="{{ asset('css/style1.css') }}" rel="stylesheet">` or custom `<style>` blocks) **MUST** be placed directly at the top of `@section('main_content')`.

---

## 2. Sidebar Navigation Integration & RBAC Architecture

### 2.1 File Location & MetisMenu Structure
- **File**: `resources/views/layouts/sidebar.blade.php`
- **Root Element**: `<div class="dlabnav"><div class="dlabnav-scroll"><ul class="metismenu" id="menu">...</ul></div></div>`
- **Client-Side Menu Engine**: MetisMenu initialized in `public/js/custom.min.js` and `public/js/custom.js` via:
  ```javascript
  var handleCurrentActive = function() {
      for (var nk = window.location,
          o = $("ul#menu a").filter(function() {
              return this.href == nk;
          })
          .addClass("mm-active")
          .parent()
          .addClass("mm-active");;) {
          if (!o.is("li")) break;
          o = o.parent().addClass("mm-show").parent().addClass("mm-active");
      }
  }
  ```
  The script automatically adds `.mm-active` to the matching `<a>` and parent `<li>`, plus `.mm-show` and `.mm-active` to ancestor `<ul>` and `<li>`.

### 2.2 Icon System
- **Library**: FontAwesome 5 / 6 Free (`fas fa-*`, `fa fa-*`) & Bootstrap Icons (`bi bi-*`).
- **Standard Sidebar Icon Style**: `<i class="fas fa-[icon-name]"></i>` followed by `<span class="nav-text">[Label]</span>`.
- **Selected Accounts Module Icon**: `<i class="fas fa-file-invoice-dollar"></i>` (or `<i class="fas fa-calculator"></i>` / `<i class="fas fa-receipt"></i>`).

### 2.3 Proposed Sidebar Markup Injection Point

In `resources/views/layouts/sidebar.blade.php`, statutory modules are ordered sequentially:
1. `Home` (`/dashboard`)
2. `Authentication` (`/branch`, `/roles`, `/user`)
3. `Customers` (`/customers`, `customer-tracking.index`)
4. `Lease Applications` (`/application`)
5. `Mining Department` (`/miningplan`, `/projectfolder`, `/process`)
6. `Environment Clearance` (`eviron.index`, `ec-certificate.index`, `ec-compliance.index`)
7. `PPT Department` (`ppt-department.index`)
8. `DGPS Survey` (`dgps-survey.index`)
9. `Drone Survey` (`drone-survey.index`)

The new **Accounts & Financials** menu group belongs immediately after `Drone Survey` (line 115) and before the commented template block. This completes the natural statutory lifecycle: from physical survey and engineering plan preparation to statutory billing and financial settlement.

```blade
{{-- Accounts & Financial Management Module --}}
@canany(['account.view', 'account.create'])
<li class="{{ request()->routeIs('accounts.*') ? 'mm-active' : '' }}">
    <a class="has-arrow {{ request()->routeIs('accounts.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('accounts.*') ? 'true' : 'false' }}">
        <i class="fas fa-file-invoice-dollar"></i>
        <span class="nav-text">Accounts</span>
    </a>
    <ul aria-expanded="{{ request()->routeIs('accounts.*') ? 'true' : 'false' }}" class="{{ request()->routeIs('accounts.*') ? 'mm-show' : '' }}">
        @can('account.view')
        <li class="{{ request()->routeIs('accounts.quotations.*') ? 'mm-active' : '' }}">
            <a href="{{ route('accounts.quotations.index') }}" class="{{ request()->routeIs('accounts.quotations.*') ? 'mm-active' : '' }}">Quotations</a>
        </li>
        @endcan

        @can('account.create')
        <li class="{{ request()->routeIs('accounts.payments.create') ? 'mm-active' : '' }}">
            <a href="{{ route('accounts.payments.create') }}" class="{{ request()->routeIs('accounts.payments.create') ? 'mm-active' : '' }}">Collect Payment</a>
        </li>
        @endcan

        @can('account.view')
        <li class="{{ request()->routeIs('accounts.ledger.*') ? 'mm-active' : '' }}">
            <a href="{{ route('accounts.ledger.index') }}" class="{{ request()->routeIs('accounts.ledger.*') ? 'mm-active' : '' }}">Customer Ledger</a>
        </li>
        <li class="{{ request()->routeIs('accounts.reports.*') ? 'mm-active' : '' }}">
            <a href="{{ route('accounts.reports.index') }}" class="{{ request()->routeIs('accounts.reports.*') ? 'mm-active' : '' }}">Financial Reports</a>
        </li>
        @endcan
    </ul>
</li>
@endcanany
```

---

## 3. UI/UX Pro Max Design Tokens & Form Patterns

### 3.1 Corporate Color Palette & Styling Tokens

Derived directly from `resources/views/pages/customer_tracking/index.blade.php` and `public/css/style1.css`:

```css
:root {
    /* Brand Navy & Accent Blues */
    --ct-primary: #0F1E4D;             /* Signature GTMS Deep Navy */
    --ct-primary-hover: #1B3A8C;       /* Navy hover tint */
    --ct-accent-blue: #2563EB;         /* Primary Action Blue */
    --ct-accent-cyan: #0284C7;         /* Info / Status Cyan */

    /* Financial State Accents */
    --ct-accent-emerald: #10B981;      /* Paid / Settled / Approved */
    --ct-accent-amber: #F59E0B;        /* Partial / Scrutiny / Warning */
    --ct-accent-red: #DC2626;          /* Pending Due / Outstanding / Critical */
    --ct-accent-purple: #7C3AED;       /* Quotation / Compliance */

    /* Neutrals & Surfaces */
    --ct-dark: #0F172A;                /* High-contrast headings */
    --ct-slate: #334155;               /* Body text */
    --ct-muted: #64748B;               /* Subtitles and field labels */
    --ct-light: #F8FAFC;               /* Background tints / Zebra stripes */
    --ct-border: #E2E8F0;              /* Card & table borders */

    /* Elevation & Shadows */
    --ct-card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.03);
    --ct-card-hover-shadow: 0 16px 32px -4px rgba(15, 23, 42, 0.09), 0 4px 12px -2px rgba(15, 23, 42, 0.04);
}
```

### 3.2 Form Component Conventions

1. **Card Headers & Containers**:
   ```html
   <div class="card border-0 shadow-sm" style="border-radius: 14px;">
       <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
           <h5 class="fw-bold text-dark mb-0"><i class="fas fa-file-invoice text-primary me-2"></i>Title</h5>
           <!-- Action Buttons -->
       </div>
       <div class="card-body p-4">
           ...
       </div>
   </div>
   ```

2. **Form Controls & Labels**:
   ```html
   <div class="mb-3 col-md-6">
       <label class="form-label fw-bold" style="color: #0F1E4D; font-size: 0.85rem;">
           Client Name <span class="text-danger">*</span>
       </label>
       <input type="text" class="form-control" name="customer_name" required>
   </div>
   ```

3. **Input Groups with Currency Prefix**:
   ```html
   <div class="input-group">
       <span class="input-group-text fw-bold text-dark" style="background: #f1f5f9; border-color: #cbd5e1;">₹</span>
       <input type="number" step="0.01" min="0" class="form-control fw-bold" name="amount" placeholder="0.00" required>
   </div>
   ```

4. **Status Badges with Subtle Tints**:
   - Paid / Full: `<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa fa-check-circle me-1"></i> Paid (Settled)</span>`
   - Partial: `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1"><i class="fa fa-hourglass-half me-1"></i> Partial Payment</span>`
   - Pending Due: `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fa fa-exclamation-circle me-1"></i> Pending Full Due</span>`

5. **Validation Error Display & Alert Banners**:
   ```blade
   @if(session('success'))
       <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3" role="alert">
           <i class="fa fa-check-circle me-2"></i>{{ session('success') }}
           <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
       </div>
   @endif

   @if(session('error'))
       <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3" role="alert">
           <i class="fa fa-circle-exclamation me-2"></i>{{ session('error') }}
           <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
       </div>
   @endif

   @if(isset($errors) && $errors->any())
       <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3" role="alert">
           <ul class="mb-0 ps-3">
               @foreach($errors->all() as $err)
                   <li>{{ $err }}</li>
               @endforeach
           </ul>
           <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
       </div>
   @endif
   ```

6. **SweetAlert2 Feedback Integration**:
   ```javascript
   Swal.fire({
       icon: 'warning',
       title: 'Validation Required',
       text: 'Paid Amount cannot exceed the remaining pending balance.',
       confirmButtonColor: '#0F1E4D'
   });
   ```

---

## 4. High-Fidelity Print Engine & Template Architecture

### 4.1 Lessons from Existing Print Layouts
The codebase features two complete, production-verified invoice print views:
1. `resources/views/pages/customer_tracking/tax_invoice.blade.php` (503 lines, single-sheet A4 layout)
2. `resources/views/pages/customer_tracking/proforma_invoice.blade.php` (585 lines, 2-page A4 multi-sheet layout)
3. `resources/views/pages/lease_application/report_pdf.blade.php` (237 lines, A4 compliance report layout)

### 4.2 Verified Print Design Pattern
All printable documents in GTMS **bypass `layouts.app`** and render as standalone HTML5 documents with inline CSS and print media queries:

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Title &bull; GTMS</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Times New Roman', Times, serif;
      background-color: #525659; /* PDF viewer screen background */
      color: #000;
      padding: 20px 0;
    }

    /* Floating Screen Action Bar */
    .no-print-bar {
      width: 210mm;
      margin: 0 auto 15px auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #0F1E4D;
      color: #fff;
      padding: 10px 20px;
      border-radius: 6px;
      font-family: system-ui, -apple-system, sans-serif;
      box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2);
    }
    .no-print-bar button, .no-print-bar a {
      background: #2563eb;
      color: #fff;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .no-print-bar a.secondary { background: #334155; }

    /* Physical Paper Simulation */
    .sheet {
      width: 210mm;
      min-height: 297mm;
      margin: 0 auto;
      background: #fff;
      padding: 14mm 14mm 10mm 14mm;
      box-shadow: 0 0 15px rgba(0,0,0,0.4);
      position: relative;
    }

    /* Print CSS Media Queries */
    @media print {
      @page {
        size: A4 portrait;
        margin: 10mm;
      }
      body {
        background: transparent;
        padding: 0;
      }
      .no-print-bar {
        display: none !important;
      }
      .sheet {
        box-shadow: none;
        margin: 0;
        padding: 0;
        width: 100%;
        min-height: auto;
      }
    }
  </style>
</head>
<body>
  <div class="no-print-bar">
    <div><strong>GTMS Document Title</strong> &bull; Ref: {{ $referenceNo }}</div>
    <div style="display:flex; gap:10px;">
      <a href="{{ route('accounts.quotations.index') }}" class="secondary">&larr; Back to List</a>
      <button onclick="window.print()">🖨️ Print / Save as PDF</button>
    </div>
  </div>

  <div class="sheet">
    <!-- Document Content -->
  </div>
</body>
</html>
```

### 4.3 Corporate Assets & Stationery Insignia

Exact asset paths present in `public/images/invoices/`:
- **Header Banner**: `asset('images/invoices/gtms_pi_banner.png')` (189 KB, full-width banner containing logo, company title, ISO certification, address, and contact numbers).
- **Standalone Brand Logo**: `asset('images/invoices/gtms_logo.png')` (51 KB, high-resolution crest).
- **Official Digital Seal / Rubber Stamp**: `asset('images/invoices/gtms_stamp.png')` (68 KB, round certified stamp).

### 4.4 Verified Legal Header & Signatory Metadata
- **Company Title**: `GEO TECHNICAL MINING SOLUTIONS`
- **Certification**: `AN ISO 9001 : 2015 CERTIFIED COMPANY`
- **Registered Office**: `1/237, AR Complex, Salem Main Road, Meyyanur, Salem – 636 004, Tamil Nadu, India.`
- **Tax IDs**: `GSTIN: 33ABCDE1234F1Z5 • PAN: ABCDE1234F`
- **Official Communications**: `Mobile: +91 94432 12345 / 94432 54321 • Email: info@gtmsmining.com • Web: www.gtmsmining.com`
- **Bank Coordinates**:
  - Account Name: `GEO TECHNICAL MINING SOLUTIONS`
  - Account No.: `38472910482`
  - Bank / Branch: `State Bank of India, Meyyanur Branch, Salem`
  - IFSC Code: `SBIN0001234`
- **Authorized Signatory**:
  - Name: `Dr. S. Karuppannan, M.Sc., Ph.D.`
  - Designation: `Managing Partner / RQP (Authorised Signatory)`

### 4.5 Dual A4 / A5 Print Rules for Receipt Vouchers
For money receipt vouchers (R3), both A4 and A5 formats are required:
- **A4 Voucher Option**:
  ```css
  @media print {
    @page { size: A4 portrait; margin: 10mm; }
  }
  ```
- **A5 Voucher Option**:
  ```css
  @media print {
    @page { size: A5 landscape; margin: 8mm; }
  }
  .receipt-voucher-a5 {
    width: 210mm;
    max-height: 148mm;
    margin: 0 auto;
    background: #fff;
    padding: 8mm 10mm;
    border: 1.5px solid #000;
  }
  ```

---

## 5. UI Specifications for the 5 Accounts Screens (R1–R5)

### Screen 1: Quotations Management Engine (R1)
**Views**:
- `resources/views/pages/accounts/quotations/index.blade.php` (List & DataTables)
- `resources/views/pages/accounts/quotations/create.blade.php` (Interactive Creator)
- `resources/views/pages/accounts/quotations/print.blade.php` (High-Fidelity A4 Print Layout)

#### 1.1 Quotations List View (`index.blade.php`)
- **Breadcrumb**: `Accounts / Quotations Directory`
- **Header Actions**: `+ Create New Quotation` button (`.btn.btn-navy`).
- **Summary Cards (4 Columns)**:
  - Total Quotations Issued (Count)
  - Total Quoted Amount (₹)
  - Quotations Converted / Accepted (Count & ₹)
  - Pending / Active Quotations (Count & ₹)
- **DataTables Columns**:
  1. `S.No`
  2. `Quotation No` (e.g. `GTMS/QTN/2026/001`) with badge
  3. `Quotation Date & Validity`
  4. `Client / Company Name` (with contact person & phone)
  5. `Quarry Concession / Location` (Village, Taluk, S.F. Nos, District)
  6. `Net Amount (₹)` (Subtotal + GST)
  7. `Status` (`Draft`, `Sent`, `Accepted`, `Expired`)
  8. `Actions` (View / Print A4, Edit, Convert to Collection, Delete)

#### 1.2 Quotation Creation Wizard (`create.blade.php`)
- **Client & Concession Auto-Populator**:
  - Client Select dropdown (`#select_quotation_customer`) prefilled with active customers (`$customers`).
  - Search by Customer Unique ID (`mimas_no` / `mimas_number`) using existing `/customers/lookup-mimas/{mimas_no}` endpoint.
  - Auto-fills: Client Name, Company Name, Mobile Number, GSTIN, PAN, Registered Address.
  - Concession Autofill: Selecting a client dynamically queries their linked `lease_applications` or allows manual input of Quarry Village, Taluk, District dropdown, Survey Numbers (e.g. `102/1A, 102/1B`), and Extent in Hectares (`area_extent_ha`).
- **Dynamic Multi-Service Line Items Table**:
  - Columns:
    1. Service Category / Pre-configured Service Template:
       - `DGPS Demarcation & Boundary Survey` (SAC: `998334`)
       - `Drone Photogrammetry & Orthomosaic Topo Survey` (SAC: `998335`)
       - `Mining Plan Preparation & Processing` (SAC: `998341`)
       - `Form-1 & Form-2 Environmental Clearance (SEIAA)` (SAC: `998342`)
       - `TNPCB Consent to Establish (CTE) / Consent to Operate (CTO)` (SAC: `998343`)
       - `Half-Yearly EC Compliance Monitoring & Filing` (SAC: `998344`)
    2. Description / Work Scope
    3. Quantity / Area Extent (Ha or Units)
    4. Unit Rate (₹)
    5. Line Subtotal (₹, auto-calculated)
    6. Action (`Remove Line`)
  - `+ Add Line Item` button dynamically appends rows.
- **GST & Calculation Box**:
  - Subtotal (Sum of line items)
  - Tax Mode Toggle: Intra-State (CGST 9% + SGST 9%) vs Inter-State (IGST 18%) vs Non-GST/Exempt
  - Automated calculation of CGST, SGST, Total Quoted Amount, and Amount in Words.
- **Statutory Terms & Scope Editor**:
  - Validity Period (default: 30 days)
  - Milestone Payment Breakdown:
    - 50% Mobilization Advance upon contract signing
    - 30% upon preparation & submission of draft statutory report
    - 20% upon final statutory clearance / approval
  - Statutory Exclusions Disclaimer: Pre-checked checkbox including standard clause:
    *"Statutory government scrutiny fees, SEIAA presentation fees, TNPCB consent application fees, and district DMF levies are to be paid directly by the client via government challans."*

#### 1.3 Quotation A4 Print Layout (`print.blade.php`)
- Full A4 sheet format matching `tax_invoice.blade.php`:
  - Screen bar with `window.print()` button.
  - GTMS corporate header (`asset('images/invoices/gtms_pi_banner.png')`).
  - Quotation metadata grid (Quotation No., Date, Validity, Prepared By).
  - Client profile & quarry concession details.
  - Detailed line items table with SAC codes and unit breakdowns.
  - GST summary and Grand Total with Amount in Words.
  - Standard Terms & Conditions and Bank Details.
  - Official Signatory Stamp (`asset('images/invoices/gtms_stamp.png')`).

---

### Screen 2: Centralized Payment Collection Interface (R2)
**Views**:
- `resources/views/pages/accounts/payments/create.blade.php` (Unified Collection Desk)
- `resources/views/pages/accounts/payments/index.blade.php` (Payment Transactions History)

#### 2.1 Customer Outstanding Dues Resolver
- **Client Selection**: Live searchable dropdown of customers.
- **Dynamic Application Dues Grid**: Upon customer selection, an AJAX call to `/accounts/customer-dues/{customerId}` queries all 7 statutory modules and renders an interactive dues resolution table:

| Select | Module | Application Reference | Concession / Quarry Details | Quoted Value (₹) | Total Paid (₹) | Pending Balance (₹) | Current Status |
|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| 🔘 | Lease Application | `LA-2026-0012` | S.F. 102/1A, Chinnagoundanur, Salem | ₹ 1,50,000.00 | ₹ 75,000.00 | **₹ 75,000.00** | `🟡 Partial` |
| 🔘 | Mining Plan | `MP-2026-0045` | S.F. 102/1A, Salem | ₹ 80,000.00 | ₹ 0.00 | **₹ 80,000.00** | `🔴 Pending` |
| 🔘 | DGPS Survey | `DGPS-2026-0018` | S.F. 102/1A, Salem | ₹ 35,000.00 | ₹ 35,000.00 | **₹ 0.00** | `🟢 Settled` |
| 🔘 | Drone Survey | `DS-2026-0008` | S.F. 102/1A, Salem | ₹ 50,000.00 | ₹ 20,000.00 | **₹ 30,000.00** | `🟡 Partial` |
| 🔘 | Environment Clearance | `EP-2026-0003` | S.F. 102/1A, Salem | ₹ 2,00,000.00 | ₹ 50,000.00 | **₹ 1,50,000.00** | `🟡 Partial` |
| 🔘 | EC Certificate | `ECC-2026-0021` | S.F. 102/1A, Salem | ₹ 40,000.00 | ₹ 0.00 | **₹ 40,000.00** | `🔴 Pending` |
| 🔘 | EC Compliance | `HYC-2026-0004` | S.F. 102/1A, Salem | ₹ 65,000.00 | ₹ 30,000.00 | **₹ 35,000.00** | `🟡 Partial` |

- **Aggregated Dues Summary Box**:
  - Total Outstanding Across All Modules: `₹ 4,10,000.00`
  - Quick-Fill button: *"Pay Full Balance for Selected Application"*

#### 2.2 Payment Entry Form
- **Selected Target**: Display target application badge and outstanding balance.
- **Payment Collection Fields**:
  1. `Amount Being Collected (₹)` (Numeric input, live validation ensuring `amount > 0` and `amount <= pending_balance`).
  2. `Payment Mode`: Radio button pills for `Cash`, `Cheque`, `NEFT/RTGS`, `UPI/GPay`.
  3. `Transaction / UTR / Cheque Reference No.`
  4. `Payment Date` (default: today).
  5. `Collecting Officer / Branch` (pre-filled with authenticated user's branch).
  6. `Notes / Remittance Particulars`.
- **Live Settlement Calculator**:
  - Current Pending Due: `₹ 75,000.00`
  - Collecting Now: `₹ 50,000.00`
  - Remaining Balance After Payment: `₹ 25,000.00`
  - Projected Status: `🟡 Partial Payment`
- **Atomic Database Submission**: Submits via POST to `accounts.payments.store`. On success, automatically prompts to print receipt voucher or downloads PDF.

---

### Screen 3: Official Money Receipt Voucher (R3)
**Views**:
- `resources/views/pages/accounts/receipts/print.blade.php` (High-Fidelity Dual A4/A5 Printable Voucher)

#### 3.1 Voucher Structure & Formatting
- **Voucher Header**: GTMS Corporate Insignia, Company Title, ISO 9001:2015 tag, Salem office address, GSTIN & PAN.
- **Voucher Title**: `OFFICIAL MONEY RECEIPT VOUCHER`
- **Receipt Metadata**:
  - Sequential Receipt Number: `GTMS/REC/2026/0042`
  - Receipt Date & Time: `29-Sep-2026 11:30 AM`
  - Payment Mode: `NEFT/RTGS` (UTR Ref: `SBIN004829104821`)
- **Customer Particulars**:
  - Received With Thanks From: `M/s. Sri Balaji Granites (P) Ltd`
  - Client Representative: `Thiru. R. Balaji`
  - Address: `Pennagaram Road, Dharmapuri, Tamil Nadu`
- **Application & Service Particulars**:
  - On Account Of: `Mining Plan Preparation & Processing`
  - Concession Location: `S.F. No. 102/1A, Chinnagoundanur Village, Sankari Taluk, Salem District`
  - Extent: `4.50 Hectares`
- **Financial Breakdown Box**:
  - Total Quoted Amount: `₹ 80,000.00`
  - Previously Received: `₹ 0.00`
  - **Amount Received Now**: **`₹ 50,000.00`**
  - **Remaining Balance Due**: `₹ 30,000.00`
- **Amount in Words**: `Rupees Fifty Thousand Only`
- **Signatory & Seal Zone**:
  - Collecting Officer Signature & Stamp.
  - Authorized Signatory stamp (`asset('images/invoices/gtms_stamp.png')`).
  - Computer-generated receipt disclaimer.

---

### Screen 4: Customer Financial Ledger & Statement of Account (R4)
**Views**:
- `resources/views/pages/accounts/ledger/index.blade.php` (Customer Financial Dossier)
- `resources/views/pages/accounts/ledger/print.blade.php` (Printable Statement of Account)

#### 4.1 Dossier Interface
- **Customer Selector & 360° Profile Header**:
  - Displays customer name, company, GSTIN, PAN, phone, address, and primary mining concessions.
  - Direct deep-links to Customer 360 Tracking (`customer-tracking.show`) and customer directory.
- **Financial Dossier KPI Metric Badges**:
  1. `Total Invoiced / Quoted Value`: `₹ 6,20,000.00`
  2. `Total Payments Collected`: `₹ 3,90,000.00`
  3. `Net Outstanding Receivables`: `₹ 2,30,000.00`
  4. `Active Application Accounts`: `5 Applications`
- **Chronological Statement Table**:

| Date | Trans Type | Reference No | Module / Description | Debit (₹ Billed) | Credit (₹ Paid) | Running Balance (₹) | Receipt / Doc |
|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|
| 10-Jan-2026 | `Quotation` | `GTMS/QTN/2026/001` | DGPS Boundary Demarcation | ₹ 35,000.00 | - | ₹ 35,000.00 | `<a href="#">QTN</a>` |
| 12-Jan-2026 | `Payment` | `GTMS/REC/2026/0002` | NEFT Adv - DGPS Survey | - | ₹ 35,000.00 | **₹ 0.00** | `<a href="#">REC</a>` |
| 02-Feb-2026 | `Quotation` | `GTMS/QTN/2026/008` | Mining Plan Preparation | ₹ 80,000.00 | - | ₹ 80,000.00 | `<a href="#">QTN</a>` |
| 15-Feb-2026 | `Payment` | `GTMS/REC/2026/0014` | Cheque #492810 - Part Pay | - | ₹ 40,000.00 | **₹ 40,000.00** | `<a href="#">REC</a>` |
| 10-Mar-2026 | `Quotation` | `GTMS/QTN/2026/015` | Form-1 Environment Clearance | ₹ 2,00,000.00 | - | ₹ 2,40,000.00 | `<a href="#">QTN</a>` |
| 20-Mar-2026 | `Payment` | `GTMS/REC/2026/0022` | RTGS Adv - EC Filing | - | ₹ 50,000.00 | **₹ 1,90,000.00** | `<a href="#">REC</a>` |

- **Statement Generator Actions**:
  - Filter by Date Range (From Date / To Date)
  - `Print Statement of Account` (opens A4 printable statement for audit and formal payment reminders)

---

### Screen 5: Centralized Financial Reports Dashboard & Reconciliation Engine (R5)
**Views**:
- `resources/views/pages/accounts/reports/index.blade.php` (Reporting Dashboard)

#### 5.1 Financial KPI Cards Grid (4 Columns)
- **KPI 1: Total Revenue Collected (All-Time)**: Sum of all completed payment collections (`₹ X,XX,XXX`).
- **KPI 2: Month-to-Date (MTD) Collections**: Total payments collected in the current calendar month with growth trend indicator.
- **KPI 3: Total Outstanding Receivables**: Statewide total pending balance across all active statutory applications.
- **KPI 4: Total Quotations Issued**: Total pipeline value and count of issued quotations.

#### 5.2 Multi-Parametric Filter Bar
- **Date Range Selector**: Preset buttons (`Today`, `This Week`, `This Month`, `This Fiscal Year`) + custom `date_from` and `date_to`.
- **Customer Filter**: Searchable select (`All Customers` or specific entity).
- **Application Module Selector**:
  - `All Modules`
  - `Lease Applications`
  - `Mining Department`
  - `Environment Clearance`
  - `PPT Department`
  - `DGPS Survey`
  - `Drone Survey`
  - `EC Certificates`
  - `EC Compliance`
- **Payment Method**: `All`, `Cash`, `Cheque`, `NEFT/RTGS`, `UPI/GPay`.
- **Settlement Status**: `All`, `Fully Settled`, `Partial Balance`, `Unpaid`.
- **Action Buttons**: `Filter Results` (`.btn.btn-navy`) and `Reset Filters`.

#### 5.3 Filtered Transaction Ledger Table
- Standard DataTables with CSV and Excel export triggers:
  - Date
  - Receipt Voucher No
  - Customer / Company Name
  - Statutory Application Ref & Module
  - Payment Mode & UTR / Cheque Ref
  - Amount Collected (₹)
  - Remaining Balance (₹)
  - Collected By Officer
  - Action (`Print Receipt`, `View Application`)

#### 5.4 Export Engine
- Dedicated export route `GET /accounts/reports/export` generating:
  - **CSV / Excel Streamed Download**: Formatted with standard accounting columns, ready for Tally/Zoho/SAP import.
  - Clean headers, escaped commas, and UTF-8 BOM encoding for Excel compatibility.

---

## 6. RBAC & Spatie Permissions Configuration (R6)

### 6.1 Defined Permission Set
| Permission Name | Module Group | Description | Assigned Default Roles |
|---|---|---|---|
| `account.view` | `account` | View quotations, collections, ledger, and reports | Admin, Super Admin, Staff, Manager |
| `account.create` | `account` | Create quotations, collect payments, issue receipts | Admin, Super Admin, Accounts Officer |
| `account.edit` | `account` | Update quotation line items, modify draft terms | Admin, Super Admin |
| `account.delete` | `account` | Cancel/void quotations, delete draft vouchers | Super Admin, Admin |

### 6.2 Seeder & Controller Integration
1. **`database/seeders/RolePermissionSeeder.php`**:
   Add to `$permissions` array:
   ```php
   // Accounts & Financials
   'account.view',
   'account.create',
   'account.edit',
   'account.delete',
   ```
2. **`app/Http/Controllers/RolesController.php`**:
   Add to `$modules` dictionary (lines 18–35):
   ```php
   'account' => 'Accounts & Financials',
   ```
   *Impact*: The Role creation and editing modal in `resources/views/pages/authentication/roles/createrole.blade.php` automatically creates an **Accounts & Financials** module card with toggleable permission checkboxes.

---

## 7. Actionable Implementation Recommendations

1. **Avoid Layout Head Editing**: Do not alter `resources/views/layouts/app.blade.php` to add style stacks. Follow codebase standard: embed `<style>` inside `@section('main_content')`.
2. **Isolate Print Views from Admin Shell**: Quotation A4 print (`print.blade.php`), Receipt Voucher (`receipt_print.blade.php`), and Customer Statement (`statement_print.blade.php`) must be independent HTML5 documents to ensure zero stylesheet contamination and 100% predictable browser PDF printing.
3. **Reuse Existing Image Assets**: Directly reference `asset('images/invoices/gtms_logo.png')`, `gtms_pi_banner.png`, and `gtms_stamp.png`. No new graphics or mock assets are needed.
4. **Leverage Existing Customer Lookup**: Use `fetch('/customers/lookup-mimas/' + encodeURIComponent(val))` for rapid quotation customer auto-fill.
5. **Ensure Strict Number-to-Words Conversion**: Implement an Indian numbering currency converter helper (`Lakh`, `Crore`) to match Tamil Nadu mining statutory standard formatting (e.g. `Rupees One Lakh Twenty-Five Thousand Only`).

---

*End of Survey Report — explorer_survey_accounts_3*

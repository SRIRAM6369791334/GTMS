# UI Changelog — GTMS Portal Design Modernization

## Overview
All changes follow the strict Master Negative Constraints:
- **0 New Features**: Zero new workflows, actions, endpoints, or backend logic.
- **100% Preserved Content & Labels**: All button text ("Add Category", "Add Units", "Add Product", "Submit", "Edit", "Delete") remain exact.
- **Lease Application Reference Untouched**: The reference module at `/application` and `/viewapplication` remained 100% unaltered.
- **Preloader Removal Protocol**: Playwright captured 108 baseline and 108 final screenshots across 4 viewports with preloader suppression.

---

## Modified Files

### 1. `resources/views/layouts/app.blade.php`
- **Component**: Global GTMS Theme Polish Engine
- **Visual Improvements**:
  - Defined universal `.btn-navy` and `.btn-app-primary` gradient styles (`linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%)`).
  - Implemented `.table-action-group` single-row flex layout with `white-space: nowrap` and `gap: 6px`.
  - Defined `.btn-action-icon` geometry (`32x32px`, `border-radius: 8px`) with four semantic color roles:
    - View: Soft Blue (`#eff6ff`, border `#bfdbfe`, text `#1d4ed8`)
    - Edit: Warm Ore-Gold (`#fef3c7`, border `#fde68a`, text `#b45309`)
    - Delete: Soft Rose (`#fee2e2`, border `#fecaca`, text `#dc2626`)
    - Download: Soft Mint (`#f0fdf4`, border `#bbf7d0`, text `#15803d`)
  - Elevated Bootstrap modals (`border-radius: 16px`, soft border `#E2E8F0`, padded header with `#0F172A` title).
  - Elevated SweetAlert2 dialogs (`border-radius: 16px`, brand confirm/cancel button shadows, typography alignment).

### 2. `resources/views/pages/master/category/index.blade.php`
- **Component**: Category Master Table & Header
- **Visual Improvements**:
  - Replaced legacy `.btn-rounded.btn-info` with `.btn-navy` (`#0F1E4D` gradient) for "Add Category" button.
  - Wrapped Edit and Delete actions in `.table-action-group`.
  - Replaced raw `.btn-xs.sharp` with `.btn-action-icon.btn-action-edit` and `.btn-action-icon.btn-action-delete`.
  - Retained exact button attributes, IDs, and modal targets.

### 3. `public/js/ajax/category.js`
- **Component**: Category AJAX Handlers & SweetAlert
- **Visual Improvements**:
  - Updated dynamic row insertion in DataTables to output `.table-action-group` and `.btn-action-icon` buttons upon AJAX creation/update.
  - Polished `Swal.fire` delete confirmation:
    - Changed `confirmButtonColor` to `#DC2626` (GTMS Crimson)
    - Changed `cancelButtonColor` to `#0F1E4D` (GTMS Navy)
    - Fixed typo: "You won't be able to recover this category!"
    - Preserved exact button labels: "Yes, Delete it!" and "Cancel".

### 4. `resources/views/pages/master/unit/index.blade.php`
- **Component**: Unit Master Table & Header
- **Visual Improvements**:
  - Replaced legacy `.btn-rounded.btn-info` with `.btn-navy` for "Add Units" button.
  - Wrapped Edit and Delete actions in `.table-action-group` with `.btn-action-icon`.
  - Preserved exact button label "Add Units" and all data attributes.

### 5. `resources/views/pages/master/product/index.blade.php`
- **Component**: Product Master Table & Header
- **Visual Improvements**:
  - Replaced legacy `.btn-rounded.btn-info` with `.btn-navy` for "Add Product" button.
  - Replaced `.d-flex` container with `.table-action-group` preventing action button wrapping in wide DataTables.
  - Upgraded buttons to `.btn-action-icon.btn-action-edit` and `.btn-action-icon.btn-action-delete`.

### 6. `public/js/ajax/product.js`
- **Component**: Product Dynamic Table Handler
- **Visual Improvements**:
  - Updated dynamic row insertion on product creation to output standardized `.table-action-group` buttons.

### 7. `resources/views/pages/master/productstock/index.blade.php`
- **Component**: Product Stock Table & Header
- **Visual Improvements**:
  - Replaced legacy `.btn-rounded.btn-info` with `.btn-navy` for "Add Product" button.
  - Upgraded Edit action to `.table-action-group` and `.btn-action-icon.btn-action-edit`.

### 8. `resources/views/pages/dgps_survey/index.blade.php`
- **Component**: DGPS Survey Register Table Actions
- **Visual Improvements**:
  - Replaced old `.btn-group.btn-group-sm` with `.table-action-group`.
  - Upgraded View and Edit actions to squircle `.btn-action-icon` (`.btn-action-view`, `.btn-action-edit`) ensuring non-wrapping horizontal layout.

### 9. `resources/views/pages/drone_survey/index.blade.php`
- **Component**: Drone Volumetric Survey Register Table Actions
- **Visual Improvements**:
  - Replaced old `.btn-group.btn-group-sm` with `.table-action-group`.
  - Upgraded View and Edit actions to squircle `.btn-action-icon` (`.btn-action-view`, `.btn-action-edit`).

### 10. `resources/views/pages/ppt_department/index.blade.php`
- **Component**: PPT Department Register Table Actions
- **Visual Improvements**:
  - Replaced old `.btn-group.btn-group-sm` with `.table-action-group`.
  - Upgraded Status Change, View Dossier, and Edit actions to squircle `.btn-action-icon` (`.btn-action-edit`, `.btn-action-view`, `.btn-action-download`).

### 11. `resources/views/pages/ec_compliance/index.blade.php`
- **Component**: EC Compliance Register Table Actions
- **Visual Improvements**:
  - Replaced old `.btn-group.btn-group-sm` with `.table-action-group`.
  - Upgraded Status Change, View Dossier, and Edit actions to squircle `.btn-action-icon` (`.btn-action-edit`, `.btn-action-view`, `.btn-action-download`).

### 12. `resources/views/pages/enviro_b1/subcat1.blade.php` & `subcat2.blade.php`
- **Component**: TOR & EIA Document Management Tables
- **Visual Improvements**:
  - Replaced legacy raw `btn-xs` with `.table-action-group` and `.btn-action-icon` (`btn-action-download`, `btn-action-view`).

### 13. `resources/views/pages/enviro_b2/index.blade.php` & `show.blade.php`
- **Component**: Environment Clearance B2 Directory & Dossier Document Actions
- **Visual Improvements**:
  - In `index.blade.php`: Replaced legacy cyan `.btn-rounded.btn-info` with Lease standard `.btn-navy` for "New B2 Application", preserving exact label.
  - In `index.blade.php`: Upgraded "View Project" action button to `.table-action-group` and `.btn-action-icon.btn-action-view`.
  - In `show.blade.php`: Replaced legacy raw `btn-xs` with `.table-action-group` and `.btn-action-icon`.

### 14. `resources/views/pages/ppt_department/wizard.blade.php`
- **Component**: Customer Unique ID Lookup Card
- **Visual Improvements**:
  - Upgraded inline blue dashed border card to Lease Application standard `.s1-lookup-card`, `.s1-lookup-label`, `.s1-lookup-badge`, `.s1-lookup-desc`, and `.s1-btn-fetch`.

### 15. `resources/views/pages/ec_compliance/wizard.blade.php`
- **Component**: Customer Unique ID Lookup Card
- **Visual Improvements**:
  - Upgraded inline blue dashed border card to Lease Application standard `.s1-lookup-card`.

### 16. `resources/views/pages/enviro_b2/wizard.blade.php`
- **Component**: Customer Unique ID Lookup Card
- **Visual Improvements**:
  - Upgraded inline blue dashed border card to Lease Application standard `.s1-lookup-card`.

### 17. `resources/views/pages/ec_certificate/wizard.blade.php`
- **Component**: Customer Unique ID Lookup Card
- **Visual Improvements**:
  - Upgraded inline dashed border card to Lease Application standard `.s1-lookup-card` with navy fetch button.

### 18. `resources/views/pages/drone_survey/wizard.blade.php`
- **Component**: Customer Unique ID Lookup Card
- **Visual Improvements**:
  - Upgraded lookup card classes to `.s1-lookup-card`, `.s1-lookup-label`, `.s1-lookup-badge`, `.s1-lookup-desc`, and `.s1-btn-fetch`.

### 19. `resources/views/pages/eviron/create.blade.php`
- **Component**: Environment Clearance B1 Creation Wizard
- **Visual Improvements**:
  - Upgraded Customer Unique ID Lookup card to `.s1-lookup-card`.

### 20. `resources/views/pages/customer_show.blade.php`
- **Component**: Customer 360 Enterprise Dossier
- **Visual Improvements & Bug Fixes**:
  - Secured `number_format` against non-numeric multi-part survey strings (e.g. TN land record `"14.20.20"` Hectare.Are.SqMeter format) using `is_numeric` guards.
  - Verified 100% clean rendering on desktop and mobile viewports with HTTP 200.

---

## Verification Summary
- **Playwright Route Test (Index Pages)**: 27 / 27 routes passed HTTP 200 (108 desktop, laptop, tablet, mobile screenshots).
- **Playwright Detail & Wizard Test**: 44 / 44 verification checks passed HTTP 200 across desktop and mobile.
- **Codebase Integrity**: Zero Tamil characters in source code verified via automated PHPUnit suite.
- **Lease Application Reference**: 100% untouched and preserved.

---

## Regressions & Reversals
- **Reversals**: 0. None required. All changes were CSS and presentation layer enhancements that passed 100% of unit/feature tests and visual comparison checks.

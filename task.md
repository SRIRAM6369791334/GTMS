# Task Tracker

## Phase 4.1: Form Data Persistence (🔴 Critical)
- [x] Add POST routes for Step 1-6 and `uploadDocument`.
- [x] Create session draft persistence (`session('lease_draft')`) in `CustomerController`.
- [x] Update blade views (Steps 1-3, 5-6) with `@csrf`, `name` attributes, and AJAX save logic.
- [x] Update Step 7 preview to read dynamically from `$previewData`.
- [x] Refactor `CustomerController::submit()` to build from the session draft.

## Phase 4.2: Process Flow Backend (🔴 Critical)
- [x] Add `validateApplication()`, `approveApplication()`, `rejectApplication()` and routes.
- [x] Add `generateReport()` and `report_pdf.blade.php`.
- [x] Update `lease_applications.status` ENUM (via migration/scratch script) to include `validated` and `revision_required`.
- [x] Create dynamic stepper (6.1-6.5) and action buttons in `viewapplication.blade.php`.

## Phase 4.3: Data Corrections (🟡 Important)
- [x] Update seeder for Rule 12 and MDCC nomenclature.
- [x] Correct Step 4 UI counter (5 affidavits).
- [x] Seed all 27 document_fields across the 3 lease application folders.
- [x] Adjust Step 5 UI to handle the 3-folder layout correctly.

## Phase 4.4: Reports & Audit (🟡 Important)
- [x] Create basic `activity_logs` entry helper (`logActivity`).
- [x] Trigger `logActivity` at key workflow transitions (Submit, Validate, Approve, Report).
- [x] Display real `activity_logs` on `viewapplication` sidebar.
- [x] (Optional) Consider model observers for automatic logging.

## Phase 4.5: Security & Backup (🟢 Nice-to-have)
- [ ] Investigate spatie/laravel-backup or protected downloads.

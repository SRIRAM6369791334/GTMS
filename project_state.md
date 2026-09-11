# GTMS — Project State & Memory

## Current Phase: PHASE 4.1-4.4 — BLUEPRINT GAP IMPLEMENTATION (COMPLETED & VERIFIED)
- **Status:** All 4 critical phases implemented and verified via automated E2E test (9/9 phases passed).
- **Last Updated:** 2026-09-10
- **E2E Test Result:** `test_complete_blueprint_flow.php` — ALL 9 TEST PHASES PASSED WITH ZERO ERRORS

### What Was Implemented:

#### Phase 4.1 — Form Data Persistence (🔴 Critical) ✅
- Added POST routes for Steps 1-6 in `routes/web.php` (saveStep1-saveStep6 + uploadDocument)
- All step forms now use AJAX POST with `@csrf` and session-based `lease_draft` storage
- Step 2 blade — added `name="contact_person"` and `name="contact_mobile"` attributes
- Step 3 blade — added `<form>`, radio inputs, dynamic categories from `$categories`
- Step 5 blade — added Plan folder (items 17-19: Plan Source File, KML, Plan PDF) → 19 total items
- Step 6 blade — form action, method, CSRF, draft fallbacks, AJAX save
- Step 7 blade — dynamic `$previewData` from session draft (not hardcoded)
- `CustomerController` methods: `saveStep1()` through `saveStep6()` + `uploadDocument()`
- `submit()` reads from `session('lease_draft')` instead of hardcoded values

#### Phase 4.2 — Process Flow Backend (🔴 Critical) ✅
- `validateApplication()` — Process Flow 6.2 (pass/fail with remarks)
- `approveApplication()` — Process Flow 6.3 (locks document set)
- `rejectApplication()` — sends back for revision with reason modal
- `generateReport()` — Process Flow 6.4 (HTML compliance dossier with Print/PDF)
- Dynamic stepper in `viewapplication.blade.php` bound to `$application->status`
- Added `validated` and `revision_required` to lease_applications status enum
- Workflow action panel with context-sensitive buttons (Validate → Approve → Download)

#### Phase 4.3 — Data Corrections (🟡 Important) ✅
- Fixed MDCC name → "Mining Dues Clearance Certificate"
- Fixed Rule 12 code → `Rule 12 (2-A)(a)`
- Added 18 document_fields for Documents folder, 6 for Lease Application, 3 for Plan
- Removed extra "Survey Reports" folder from lease module
- Fixed Step 4 count: "Form + 5 affidavits" (was 4)
- Step 5 now shows all 3 folders with 19 items total

#### Phase 4.4 — ActivityLog Integration (🟡 Important) ✅
- `logActivity()` helper using `loggable_type`/`loggable_id` columns
- Logs: `application_submitted`, `data_validated`, `application_approved`, `report_generated`
- Real timeline displayed in viewapplication sidebar from `activity_logs` table
- `mimasCredential()` HasOne relationship added to LeaseApplication model

### Verification:
- Application LA-2026-0006: Created → Validated → Approved → Report Generated
- 19 physical files on disk across 3 folders (9 Documents + 7 Lease App + 3 Plan)
- 4 audit log entries automatically created
- Report HTML: 12,791 bytes with all applicant, lease, document, and MIMAS data
- Browser verified: Dossier page renders with dynamic stepper, workflow buttons, and timeline

## Summary of Delivered Architecture:
1. **40 Database Tables Active + Extended Enum:**
   - `lease_applications.status` enum now includes `validated` and `revision_required`
   - 27 document_fields seeded across 3 lease folders (Documents: 18, Lease Application: 6, Plan: 3)
   - 8 Lease Categories with corrected codes and names
   - 3 Lease Folders (removed extra "Survey Reports")
2. **Session-Based Draft Persistence:**
   - All wizard steps save to `session('lease_draft')` via AJAX POST
   - Draft cleared after successful submission
   - File uploads stored in `public/uploads/lease_drafts/{draft_id}/` until submission
3. **Complete Process Flow (6.1-6.4):**
   - 6.1 Upload & Store → automatic on submission
   - 6.2 Validate Data → `POST /application/{id}/validate` (pass/fail)
   - 6.3 Approve Data → `POST /application/{id}/approve`
   - 6.4 Generate Reports → `GET /application/{id}/report` (printable HTML dossier)
4. **Security & Data Isolation:** AES-256 encrypted MIMAS passwords, `BelongsToBranch` trait.

## Recent Updates (2026-09-11):
- **Step 7 Dynamic Preview Verification & Cleanup**:
  - Removed all hardcoded fallbacks across `createstep7.blade.php`, `CustomerController.php`, `createstep4.blade.php`, and `createstep6.blade.php`.
  - Preview in Step 7 now strictly and only displays real data entered by the applicant across Steps 1 through 6.
  - Step 1: added Mineral Type selection and auto-binding from MIMAS lookup.
  - Step 4: dynamic category rule name in guidance banner.
  - Step 5 & 7: dynamic 3-folder upload checklist counters and actual file attachment badges.
  - Step 6: manual-only MIMAS credential entry with show/hide password toggle and encrypted persistence.

- **View Application Dossier (`/viewapplication`) Audit & 100% Dynamic Upgrade (COMPLETED & VERIFIED) ✅**:
  - **Identified & Purged Hardcoded Elements**:
    - Replaced static "Complete" / "Verified" KPI badges with dynamic status indicators reflecting real DB document counts (`$regCount >= 16`, `$planCount >= 3`, `$mimas->portal_status`).
    - Replaced hardcoded "19 total items" and "all 19 files" text with dynamic `{{ $docs->count() }}`.
    - Fixed Timezone mismatch (`APP_TIMEZONE=Asia/Kolkata`) which previously caused Carbon to report "4 hours from now".
  - **Added "Lease & Applicant Information" Dossier Card**:
    - Now displays all authentic applicant data entered in Steps 1-6 (Customer, Mineral, District, Taluk, Extent in Ha, Survey Nos, Lease Category, Lease Period, Aadhaar, PAN, GSTIN, Contact info).
  - **Clean 1-19 Document Sorting**:
    - Sorted all attached files numerically (Item 1 through Item 19) with direct file view links to verified files in `public/uploads/lease_applications/{app_no}/`.
  - **Verified via Automated Test**:
    - Compiled view verified with zero errors (HTML length: 62,466 bytes, all fields confirmed).

  - **Scrutiny Dossier Document Status & Honest State Resolution (COMPLETED & VERIFIED) ✅**:
    - **Eliminated Fake File Auto-Generation**: Removed legacy demo logic in `CustomerController@submit` that created fake ~800-byte PDF/KML files and assigned hardcoded initial statuses (`validated` / `uploaded`) when no documents were uploaded.
    - **Honest Document States**: Non-uploaded files now strictly remain `status = 'pending'`, `file_path = null`, showing `"Not uploaded by applicant"` and `"Pending"` badges with a direct `"Upload"` link.
    - **Dual-Level Validation Engine**:
      1. **Bulk Stage Validation (Process Flow 6.2)**: "Pass Validation" button marks the entire application as `validated` and automatically upgrades all real uploaded documents (`whereNotNull('file_path')`) to `status = 'validated'`.
      2. **Granular Per-Document Scrutiny**: Interactive action column with `[✓ Mark Valid]`, `[✗ Flag for Revision]` (with correction note modal), and `[Undo]` buttons powered by real-time AJAX (`POST /application/document/{id}/status`).
    - **Real-Time Audit Trail**: Every document status change or officer note is logged to `activity_logs` in real time with IST timestamps.
    - **Application #7 Reset**: Purged legacy placeholder files on disk and reset all 19 document slots to `pending` to honestly reflect the applicant's upload actions.

## Next Steps:
- Phase 4.5: Security & Backup (spatie/laravel-backup, protected file downloads)
- Mining Plan 6.1-6.6 stage validation workflow binding


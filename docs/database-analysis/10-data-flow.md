# 10 - Data Flow Across the System

Describes how data moves through the application lifecycle: from intake via wizards, through folders/documents, validation, approval, reporting, and archival — across all modules.

---

## 10.1 Generic Module Lifecycle

Every wizard module (Lease, Mining, Environment, PPT, DGPS, Drone, EC) follows the same lifecycle pattern:

```
INTAKE ──► PROJECT ──► FOLDERS ──► DOCUMENTS ──► VALIDATE ──► APPROVE ──► REPORT ──► ARCHIVE
(step 1-3)  (create row) (master)   (project_documents)  (project_flows)   (status)   (status)
```

### Stage 1 — Intake (Wizard Steps)
- User fills wizard fields → writes to the module's project table (`mining_applications`, `environment_projects`, etc.).
- Each step may be a separate AJAX call or a single submit. **Currently mostly static; backend wiring pending.**
- Master lookups (district, mineral, category, plan type) are read in as dropdowns/radios.

### Stage 2 — Folder & Document Setup
- `<module>.folios` are displayed from the `folders` master (per module).
- Each folder maps to checklists from `document_fields`.
- On submit, `project_documents` rows are created for **each checklist item** with status `pending`.

### Stage 3 — Upload
- User drag-drops a file into a checklist row → `project_documents` updated: `file_path`, `file_name`, `file_type`, `file_size`, `status=uploaded`, `uploaded_by`, `uploaded_at`.

### Stage 4 — Validation (Process)
- Supervisor reviews each document → review modal sets `status` (validated/approved/revision_required) + `review_note`.
- `project_flows` step rows (e.g. 6.1 Upload & Store → 6.6 Logout) each get status passed/rejected.
- Every action appended to `activity_logs`.

### Stage 5 — Approval & Reporting
- Project `status` moves draft → validation → approved → reported → archived.
- Reports (GTM/GTMS portal links or report files) attached to the project/report columns or `project_documents`.

### Stage 6 — Archival
- Status archived; documents retained; activity history preserved.

---

## 10.2 Data Flows By Module

### Dashboard → 
- Queries aggregated counts from each project table's status.
- KPI cards: active applications, pending actions = `COUNT(*) ... WHERE status IN (...)`. **Derived, not stored.**
- Charts + recent activity from `activity_logs`.

### Customer → Projects
- New customer created via directory OR inline in wizard (mining step 1). **Decision:** one canonical `customers` table; wizards either select existing or create on the fly.

### Environment B2 (working reference implementation)
The only module with a real backend, so it is the **reference flow**:
1. `POST /environment-b2/store` → create `environment_projects` (status draft).
2. Wizard advances; `POST /environment-b2/{id}/documents` → create checklist docs.
3. `POST /environment-b2/documents/upload` → store file, set uploaded.
4. `POST /environment-b2/documents/{id}/review` → set status + note.
5. `POST /environment-b2/{id}/status` → advance project status.
6. Activity log rows appended throughout.

---

## 10.3 Cross-Module Sharing (Document the linkage)

| Source | Target | How Linked | Status |
| ------ | ------ | ---------- | ------ |
| Lease Application | Mining Application | same customer / site | 🟡 (needs confirm) |
| Environment Project | EC Certificate | ec_certificates.environment_project_id | 🟡 |
| Mining Application | DGPS/Drone Survey | survey_no references lease/mining site | 🟡 |
| Customer | All projects | customer_id | 🔵/🟡 |

**Open question:** Are these independent silos or is there an overarching **master project/lease** that all survey/EC/PPT records attach to? → doc 16.

---

## 10.4 Reporting Flow

- Reports are **generated** (not stored as aggregates): count by status, by district, by mineral, by category, progress %, timeliness.
- Report files (PDF) stored via `project_documents` (folder Report / placeholder columns).

---

## 10.5 Audit Trail Flow
- `activity_logs` (loggable_type/id polymorphic) captures create/update/status-change/upload/review actions with user + timestamp.
- Powers "Recent Activity", "Process/Audit" screens, and notification feed.

---

## 10.6 Notifications Flow
- `notifications` (DB channel) generated on: new document pending review, status change, milestone reached.
- Feed by recipient user via Laravel Notifications.

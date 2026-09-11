# GTMS — Database Architecture Master Document

**Granite / Mining Tracking Management System** · District Mining Office Management System
**Version:** 1.0 (Analysis) · **Date:** 2026-09-04 · **Scope:** Database Architecture (UI→DB design)

---

## 1. Purpose

This is the **master reference** for the GTMS relational database design. It consolidates the findings of the 16 companion documents in `/docs/database-analysis/` and defines the recommended 3NF schema, to be reviewed and approved **before** any migrations/models are written.

> **Rule:** No UI code is modified and no backend/migrations created until this architecture is approved.

---

## 2. Companion Documents Index

| Doc | Title |
| --- | ----- |
| 01 | UI Project Inventory |
| 02 | Page-by-Page and Form Analysis |
| 03 | Complete Input Field Analysis |
| 04 | Data Classification |
| 05 | Entity Discovery & Normalization |
| 06 | Table Design |
| 07 | Relationship Diagram (ERD) |
| 08 | Master & Lookup Tables |
| 09 | UI ↔ Database Mapping |
| 10 | Data Flow |
| 11 | Indexing Strategy |
| 12 | Migration Plan |
| 13 | Model Plan |
| 14 | Validation Plan |
| 15 | Data Dictionary |
| 16 | Unknown Business Requirements |

---

## 3. Executive Summary

GTMS is a District Mining Office system with multiple wizard-driven workflow modules (Lease Application, Mining Plan, Environment Clearance B1/B2, EC Certificate, PPT Department, DGPS Survey, Drone Survey) sharing a common **polymorphic folder/document** structure, plus Auth (users/roles/branches/permissions), Customer directory, and an (out-of-scope) Inventory master.

**Key architectural decision:** Rather than creating a separate document table per module, all modules share:
- `project_documents` (polymorphic) — every uploaded file,
- `project_flows` (polymorphic) — validation/approval steps,
- `activity_logs` (polymorphic) — audit trail,
- `folders` + `document_fields` masters — the checklists.

Master lists (`districts`, `minerals`, `lease_categories`, `plan_types`, `applicant_types`, `modules`, `folders`, `document_fields`) eliminate repeated string values across thousands of application rows (3NF).

---

## 4. Design Principles
1. **3NF normalization** — no repeated groups, no partial/transitive dependencies.
2. **Polymorphic shared tables** for document/flow/audit to avoid fragmenting identical logic per module.
3. **Master tables** replace repeated hardcoded dropdowns/radios.
4. **Derived data never stored** — status counts, folder progress, stock, dashboard KPIs are computed.
5. **Confidence-tagged** — every design point marked 🔵 CONFIRMED / 🟢 INFERRED / 🟡 NEEDS CONFIRMATION.

---

## 5. Architecture Overview Diagram

```
                             ┌────────────────────────────┐
                             │      ACCESS & MASTERS       │
                             │ users / roles / permissions │
                             │ departments / districts /   │
                             │ minerals / categories /     │
                             │ plan_types / applicant_types│
                             └───────────────┬─────────────┘
                                             │ FK references
        ┌────────────────────────────────────┼─────────────────────────────────────┐
        │                                    │                                     │
   CUSTOMERS                    ┌────────────▼────────────┐                  MODULES/FOLDERS/
        │  1:M                  │    CORE PROJECT TABLES  │                  DOCUMENT_FIELDS
        │   ┌─────────────────► │ lease_applications      │  ◄─────────────── (checklist masters)
        │   │                   │ mining_applications     │
        │   │                   │ environment_projects    │
        │   │                   │ ec_certificates         │
        │   │                   │ ppt_applications        │
        │   │                   │ dgps_surveys            │
        │   │                   │ drone_surveys           │
        │   │                   └────────────┬────────────┘
        │   │                                │ polymorphic (documentable/flowable/loggable)
        │   │      ┌─────────────────────────┼──────────────────────────┐
        │   │      ▼                         ▼                          ▼
        │   │  project_documents        project_flows            activity_logs
        │   │  (files + status)         (workflow steps)          (audit trail)
        │   │                                │
        │   └── mimas_credentials (1:1 lease)
        └──  notifications (feed)
```

---

## 6. Core Schema (summary)

### 6.1 Identity
- **users** — name, email, mobile_num, role_id, branch_id, image, password, show_password, status
- **departments** (branch) — branch_name, contact_person, mobile, address, city, state, pincode, status
- **roles** · **permissions** · **role_permission** (pivot)

### 6.2 Masters
- **districts** · **minerals** · **lease_categories** · **plan_types** · **applicant_types** · **modules**
- **folders** (module-keyed) · **document_fields** (folder-keyed checklist)
- (inventory) **units** · **product_categories** · **products**

### 6.3 Core Projects
- **customers** · **lease_applications** (+ **mimas_credentials** 1:1) · **mining_applications** · **environment_projects** · **ec_certificates** · **ppt_applications** · **dgps_surveys** · **drone_surveys**

### 6.4 Cross-cutting (polymorphic)
- **project_documents** · **project_flows** · **activity_logs** · **notifications**

---

## 7. Full Column Definitions
See **`06-table-design.md`** for complete table-by-table column listings, types, keys and confidence.

---

## 8. Key Decisions & Rationale

### D1 — One polymorphic `project_documents` (NOT per-module tables)
- **Why:** Every module has an identical "folder → checklist → upload → review" pattern. 7 duplicated document tables would fragment logic, queries, and maintenance. A single polymorphic table + `folders`/`document_fields` masters serves all modules uniformly and mirrors the already-working Environment B2 backend.
- **Confidence:** 🔵 (pattern confirmed, exact folder/document master content 🟡).

### D2 — `customers` as canonical party + nullable FK on projects
- **Why:** Client data repeats across all wizards. Centralizing removes duplication. Wizards either select an existing customer or create one inline.
- **Confidence:** 🟡 (see 16 Q1).

### D3 — Master tables for dropdowns/radios
- **Why:** 3NF; values like district/mineral/category appear in every module.
- **Confidence:** 🟡 (see 16 Q2–Q4).

### D4 — Polymorphic `project_flows` for validation (6.1–6.6 etc.)
- **Why:** Workflow steps are identical conceptually across modules.
- **Confidence:** 🟢.

### D5 — Polymorphic `activity_logs`
- **Why:** Powers "recent activity", process/audit screen, and optional notification feed.
- **Confidence:** 🔵.

### D6 — Derived data not stored
- **Why:** Dashboard counts, folder progress %, stock totals are computed at read time.
- **Confidence:** 🔵.

---

## 9. Migration / Build Sequence
See **`12-migration-plan.md`** (31 migrations, 30 seeders, topological order).

---

## 10. Open Items Before Approval 🔴
1. Customer vs application identity model (16 Q1).
2. Cross-module linkage / overarching master application (16 Q5).
3. Inventory module scope (16 Q6).
4. MIMAS encryption confirmation (16 Q10).
5. Exact permission key list + role seed names (16 Q7).
6. Status enumerations consistency across modules (16 Q12).

All other items can proceed (or be designed in as masters) during implementation.

---

## 11. Confidence Summary
| Level | Meaning | Coverage |
| ----- | ------- | -------- |
| 🔵 CONFIRMED FROM UI | Directly observed in blade/JS/controller | identity, customers, env B2, masters CRUD, project tables, documents |
| 🟢 INFERRED FROM UI STRUCTURE | Reasoned from repeated patterns | workflow/flow steps, modules |
| 🟡 NEEDS BUSINESS CONFIRMATION | Design decision | master centralization, cross-links, inventory, permissions | 

---

*End of master document. Companion docs contain full detail.*

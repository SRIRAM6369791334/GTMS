# 17 — Improved Schema Design v2
## GTMS: Real Foreign Keys · Large-Scale Partitioning · Historical/Archival Data

**Supersedes:** the polymorphic pattern in `06-table-design.md` §6.4 / `07-relationship-diagram.md` §7.4–7.5
**Fixes:** open items 16.1 #5 (cross-module linkage), #12 (status consistency), plus scale + archival gaps not covered in the original 16 docs.

---

## 1. The core problem with the current draft

`project_documents`, `project_flows`, `activity_logs` use `*_type` / `*_id` polymorphism to attach to any of the 7 module tables (`lease_applications`, `mining_applications`, `environment_projects`, `ec_certificates`, `ppt_applications`, `dgps_surveys`, `drone_surveys`).

This works in Laravel's ORM but **breaks at the database level**:

- No `FOREIGN KEY` can point at "one of 7 possible tables" — MySQL/Postgres FK constraints target exactly one table.
- Orphan rows (a document pointing at a deleted `mining_applications` row) are only prevented by application code, never by the DB.
- Every cross-module report (e.g. "all documents across all modules for customer X") needs 7-way UNIONs.
- Q5 in your `16-unknown-business-requirements.md` — "should `ec_certificates` link to `environment_projects`, should surveys reference a lease" — has no clean home in this structure.

## 2. The fix: a `projects` registry (class-table inheritance)

Instead of 7 independent root tables, introduce **one root table** that every case belongs to, and turn the 7 module tables into **extension tables** hanging off it 1:1.

```
                         ┌─────────────────────────────┐
                         │          projects           │  ◄── the ONE root every module extends
                         │  id, module_id, customer_id, │
                         │  reference_no, status_id,    │
                         │  district_id, created_by,    │
                         │  created_at, deleted_at       │
                         └───────────────┬──────────────┘
                                         │ id  (real FK from every side below)
        ┌──────────────┬────────────────┼────────────────┬───────────────┐
        ▼              ▼                ▼                ▼               ▼
lease_applications  mining_applications  environment_projects  ppt_applications  dgps_surveys / drone_surveys
  (project_id PK+FK)   (project_id PK+FK)   (project_id PK+FK)   (project_id PK+FK)   (project_id PK+FK)
        │
        ▼
  ec_certificates (project_id PK+FK — its own case, LINKED to an environment_project via project_links)

        ┌───────────────────────────────────────────────────────────────┐
        ▼                          ▼                                    ▼
project_documents            project_flows                        activity_logs
 (project_id — REAL FK)      (project_id — REAL FK)               (project_id — REAL FK, nullable
                                                                     for non-project system events)
        ▼
project_links (project_id, linked_project_id, link_type)   ← replaces ad-hoc environment_project_id
```

**Why this solves everything at once:**

| Problem | How the registry fixes it |
|---|---|
| No real FK on documents/flows/logs | `project_documents.project_id BIGINT UNSIGNED NOT NULL REFERENCES projects(id)` — one target table, real constraint, `ON DELETE RESTRICT` |
| Cross-module linkage (Q5) | `project_links` is a plain self-referencing many-to-many on `projects` — lease↔mining↔EC↔survey all use the same mechanism, no per-pair hack column |
| Status consistency (Q12) | `projects.status_id` is one FK to one `statuses` master for every module; each module table can still have a `sub_status` for its own workflow detail |
| Reporting across modules | `SELECT * FROM projects WHERE customer_id = ?` already gives you every case type without a 7-way UNION |
| Dashboards / KPIs | Computed from `projects` + `status_id`, still derived-not-stored (keeps your original D6 principle) |

Each module table (`lease_applications` etc.) keeps **all its module-specific columns exactly as your `06-table-design.md` defined them** — the only structural change is:
- its PK becomes `project_id` (not its own `id`), and
- `project_id` is both PK and FK → `projects.id` (1:1 "extension table").

This is a minimal, additive change to your existing design — not a rewrite.

---

## 3. DDL — the new/changed tables

```sql
-- ============================================================
-- 3.1  STATUSES MASTER (replaces the free-text status columns
--      scattered across every module — resolves Q12)
-- ============================================================
CREATE TABLE statuses (
    id            BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    module_id     BIGINT UNSIGNED NULL,          -- NULL = applies to all modules
    code          VARCHAR(50)  NOT NULL,          -- draft, submitted, validation, approved, reported, archived
    label         VARCHAR(100) NOT NULL,
    sort_order    SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    is_terminal   BOOLEAN NOT NULL DEFAULT FALSE, -- true = no further transitions (approved/rejected/archived)
    created_at    TIMESTAMP NULL,
    updated_at    TIMESTAMP NULL,
    UNIQUE KEY uq_status_module_code (module_id, code),
    CONSTRAINT fk_status_module FOREIGN KEY (module_id) REFERENCES modules(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- 3.2  PROJECTS — the root registry every module attaches to
-- ============================================================
CREATE TABLE projects (
    id             BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    module_id      BIGINT UNSIGNED NOT NULL,
    customer_id    BIGINT UNSIGNED NULL,
    district_id    BIGINT UNSIGNED NULL,
    reference_no   VARCHAR(50) NOT NULL,           -- unifies application_no/survey_no/ec_ref_no
    status_id      BIGINT UNSIGNED NOT NULL,
    created_by     BIGINT UNSIGNED NULL,
    created_at     TIMESTAMP NULL,
    updated_at     TIMESTAMP NULL,
    deleted_at     TIMESTAMP NULL,                 -- soft delete
    archived_at    TIMESTAMP NULL,                 -- see §5 archival strategy

    UNIQUE KEY uq_projects_reference_no (reference_no),
    KEY idx_projects_module   (module_id),
    KEY idx_projects_customer (customer_id),
    KEY idx_projects_district (district_id),
    KEY idx_projects_status   (status_id),
    KEY idx_projects_created  (created_at),         -- for range queries / partitioning maintenance

    CONSTRAINT fk_proj_module   FOREIGN KEY (module_id)   REFERENCES modules(id)    ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_proj_customer FOREIGN KEY (customer_id) REFERENCES customers(id)  ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_proj_district FOREIGN KEY (district_id) REFERENCES districts(id)  ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_proj_status   FOREIGN KEY (status_id)   REFERENCES statuses(id)   ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_proj_creator  FOREIGN KEY (created_by)  REFERENCES users(id)      ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB
  PARTITION BY RANGE (YEAR(created_at)) (   -- see §6 for rationale
    PARTITION p_2023 VALUES LESS THAN (2024),
    PARTITION p_2024 VALUES LESS THAN (2025),
    PARTITION p_2025 VALUES LESS THAN (2026),
    PARTITION p_2026 VALUES LESS THAN (2027),
    PARTITION p_future VALUES LESS THAN MAXVALUE
  );

-- ============================================================
-- 3.3  MODULE EXTENSION TABLES (example: lease_applications)
--      Same pattern applies to mining_applications,
--      environment_projects, ec_certificates, ppt_applications,
--      dgps_surveys, drone_surveys.
-- ============================================================
CREATE TABLE lease_applications (
    project_id      BIGINT UNSIGNED PRIMARY KEY,   -- PK == FK, enforces strict 1:1
    category_id     BIGINT UNSIGNED NULL,
    contact_person  VARCHAR(255) NULL,
    contact_mobile  VARCHAR(15)  NULL,
    survey_no       VARCHAR(100) NULL,
    taluk           VARCHAR(255) NULL,
    village         VARCHAR(255) NULL,
    area_extent     DECIMAL(10,2) NULL,
    mineral_id      BIGINT UNSIGNED NULL,
    lease_period    SMALLINT UNSIGNED NULL,

    CONSTRAINT fk_lease_project  FOREIGN KEY (project_id)  REFERENCES projects(id)         ON DELETE CASCADE,
    CONSTRAINT fk_lease_category FOREIGN KEY (category_id) REFERENCES lease_categories(id) ON DELETE SET NULL,
    CONSTRAINT fk_lease_mineral  FOREIGN KEY (mineral_id)  REFERENCES minerals(id)         ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- 3.4  PROJECT_LINKS — resolves cross-module linkage (Q5)
--      e.g. an ec_certificate's project links to the
--      environment_project's project; a dgps_survey links
--      to the mining_application it was surveyed for.
-- ============================================================
CREATE TABLE project_links (
    id               BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    project_id       BIGINT UNSIGNED NOT NULL,     -- the "child"/dependent project
    linked_project_id BIGINT UNSIGNED NOT NULL,    -- the project it depends on / relates to
    link_type        VARCHAR(50) NOT NULL,         -- 'ec_certificate_of' | 'survey_for' | 'revision_of' ...
    created_at       TIMESTAMP NULL,

    UNIQUE KEY uq_link (project_id, linked_project_id, link_type),
    KEY idx_link_reverse (linked_project_id),
    CONSTRAINT fk_link_project        FOREIGN KEY (project_id)        REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT fk_link_linked_project FOREIGN KEY (linked_project_id) REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT chk_link_no_self CHECK (project_id <> linked_project_id)
) ENGINE=InnoDB;

-- ============================================================
-- 3.5  PROJECT_DOCUMENTS — now with a REAL foreign key
-- ============================================================
CREATE TABLE project_documents (
    id                BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    project_id        BIGINT UNSIGNED NOT NULL,     -- real FK, was documentable_type/id
    folder_id         BIGINT UNSIGNED NULL,
    document_field_id BIGINT UNSIGNED NULL,
    file_name         VARCHAR(255) NULL,
    file_path         VARCHAR(255) NULL,             -- object storage key (S3/GCS), never a DB blob
    file_type         VARCHAR(10)  NULL,
    file_size         BIGINT UNSIGNED NULL,
    status_id         BIGINT UNSIGNED NOT NULL,
    review_note       TEXT NULL,
    reviewed_by       BIGINT UNSIGNED NULL,
    reviewed_at       TIMESTAMP NULL,
    uploaded_by       BIGINT UNSIGNED NULL,
    uploaded_at       TIMESTAMP NULL,
    created_at        TIMESTAMP NULL,
    deleted_at        TIMESTAMP NULL,

    KEY idx_docs_project        (project_id),
    KEY idx_docs_project_folder (project_id, folder_id),
    KEY idx_docs_folder         (folder_id),
    KEY idx_docs_status         (status_id),

    CONSTRAINT fk_doc_project FOREIGN KEY (project_id) REFERENCES projects(id)        ON DELETE CASCADE,
    CONSTRAINT fk_doc_folder  FOREIGN KEY (folder_id)  REFERENCES folders(id)         ON DELETE SET NULL,
    CONSTRAINT fk_doc_field   FOREIGN KEY (document_field_id) REFERENCES document_fields(id) ON DELETE SET NULL,
    CONSTRAINT fk_doc_status  FOREIGN KEY (status_id)  REFERENCES statuses(id)        ON DELETE RESTRICT
) ENGINE=InnoDB
  PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p_2023 VALUES LESS THAN (2024),
    PARTITION p_2024 VALUES LESS THAN (2025),
    PARTITION p_2025 VALUES LESS THAN (2026),
    PARTITION p_2026 VALUES LESS THAN (2027),
    PARTITION p_future VALUES LESS THAN MAXVALUE
  );

-- ============================================================
-- 3.6  ACTIVITY_LOGS — append-only, real FK, heaviest table
--      (kept nullable project_id for system-level events)
-- ============================================================
CREATE TABLE activity_logs (
    id           BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    project_id   BIGINT UNSIGNED NULL,               -- real FK now; NULL = system event (login, etc.)
    user_id      BIGINT UNSIGNED NULL,
    action       VARCHAR(100) NOT NULL,
    description  TEXT NULL,
    properties   JSON NULL,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    KEY idx_log_project_created (project_id, created_at),
    KEY idx_log_user            (user_id),
    KEY idx_log_created         (created_at),

    CONSTRAINT fk_log_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE SET NULL,
    CONSTRAINT fk_log_user    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE SET NULL
) ENGINE=InnoDB
  PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p_2023 VALUES LESS THAN (2024),
    PARTITION p_2024 VALUES LESS THAN (2025),
    PARTITION p_2025 VALUES LESS THAN (2026),
    PARTITION p_2026 VALUES LESS THAN (2027),
    PARTITION p_future VALUES LESS THAN MAXVALUE
  );
```

> **Note on partitioned tables + FKs:** MySQL/InnoDB does not allow a partitioned table to be the *target* of a foreign key from another table, and a partitioned child table's FK columns must be part of the partitioning key indirectly satisfied. In practice this means: `projects` (partitioned) can still safely be *referenced by* `project_documents`/`activity_logs` — that direction is fine. If you outgrow MySQL's partitioning limits here, the same result is achieved on Postgres with native declarative partitioning + normal FKs, or on MySQL by moving to **time-based archive tables** instead of live partitions (§5.2) — pick whichever your DBA is more comfortable operating.

---

## 4. Full relationship summary (replaces `07-relationship-diagram.md` §7.4–7.6)

| From | To | Cardinality | Real FK? |
|---|---|---|---|
| projects | modules | *:1 | ✅ |
| projects | customers | *:1 | ✅ |
| projects | statuses | *:1 | ✅ |
| lease_applications / mining_applications / environment_projects / ec_certificates / ppt_applications / dgps_surveys / drone_surveys | projects | 1:1 (PK=FK) | ✅ |
| project_links | projects (both sides) | *:* self-ref | ✅ |
| project_documents | projects | *:1 | ✅ (was polymorphic) |
| project_flows | projects | *:1 | ✅ (was polymorphic) |
| activity_logs | projects | *:1 nullable | ✅ (was polymorphic) |
| mimas_credentials | lease_applications | 1:1 | ✅ (project_id) |
| project_documents | folders / document_fields | *:1 | ✅ |
| folders | modules | *:1 | ✅ |
| users | roles / departments | *:1 | ✅ |
| role_permission | roles / permissions | *:* pivot | ✅ |

**Result: zero polymorphic associations left in the schema — every relationship is a standard, database-enforced foreign key.**

---

## 5. Handling long-lost / historical data

This was missing from the original 16 docs entirely — here's the full policy.

### 5.1 Soft delete everywhere it matters
`deleted_at` on: `projects`, all extension tables (via `projects.deleted_at` — no need to duplicate), `customers`, `project_documents`. A "deleted" case is never physically gone; it's filtered out of normal queries (`WHERE deleted_at IS NULL`) but fully recoverable and still FK-valid.

### 5.2 Archival tier (for records years old, rarely queried)
Two viable strategies — pick one based on your ops maturity:

**A. Same-schema, `archived_at` flag (simplest, recommended to start)**
- `projects.archived_at` set by a scheduled job once `status` is terminal (approved/rejected) **and** older than e.g. 3 years.
- Archived rows stay in the same table (still fast to `JOIN`, still FK-safe) but are excluded from default dashboard queries (`WHERE archived_at IS NULL`).
- A partial/filtered index (`WHERE archived_at IS NULL`) on Postgres, or a leading `archived_at` column in composite indexes on MySQL, keeps "active work" queries fast even as the archived tail grows to millions of rows.

**B. Cold-storage move (for true multi-decade retention / regulatory archives)**
- A nightly job copies whole project graphs (`projects` + all extension rows + `project_documents` + `activity_logs`) older than N years into a separate **archive schema/database** (`gtms_archive`) with the *same table structure*.
- Deletes them from the live database after copy is verified.
- `project_documents.file_path` already points at object storage (S3/GCS), not DB blobs, so archiving the DB rows doesn't move actual files — you separately apply an S3 lifecycle rule (Standard → Infrequent Access → Glacier) keyed off the same age threshold.
- A single `reference_no` lookup UI can check live DB first, then archive DB, so old cases (e.g. a lease from 2016) are still retrievable, just not part of hot-path queries.

**Recommendation:** start with (A) — it's zero extra infrastructure and solves 90% of the pain (keeping the live table performant). Move to (B) only if you have a firm regulatory retention/purge requirement or the live DB size becomes an operational problem (multi-hundred-GB).

### 5.3 Full audit trail (already partly in your design, now real-FK'd)
`activity_logs` becomes the permanent "who did what, when" ledger with a real `project_id` FK — this is your source of truth for "long lost data" questions like *"who approved this in 2022 and what did the file look like then"*. Because it's append-only and partitioned by year (§6), it stays cheap to write and query even at very large row counts.

### 5.4 Versioning documents (optional, if re-uploads should keep history)
If a citizen/officer can re-upload a corrected document and you need to keep the old version (not just overwrite), add:
```sql
ALTER TABLE project_documents ADD COLUMN supersedes_document_id BIGINT UNSIGNED NULL;
ALTER TABLE project_documents ADD CONSTRAINT fk_doc_supersedes
    FOREIGN KEY (supersedes_document_id) REFERENCES project_documents(id) ON DELETE SET NULL;
```
This turns each re-upload into a new row linked to the one it replaces — the full document history per checklist item is queryable without a separate versions table.

---

## 6. Large-scale performance strategy

| Concern | Approach |
|---|---|
| **Which tables grow unbounded** | `project_documents`, `activity_logs`, `projects` (steady linear growth with case volume). Everything else (masters, users, folders) stays small forever. |
| **Partitioning** | RANGE partition the 3 growing tables by `YEAR(created_at)` (shown in §3). Query planner prunes to 1 partition for "this year's dashboard", keeps old partitions untouched. Add a new partition each Dec via a scheduled job (`ALTER TABLE ... REORGANIZE PARTITION p_future INTO (...)`). |
| **Indexing** | Your original `11-indexing-strategy.md` composite-index list is still correct and now simpler: every polymorphic `(x_type, x_id)` composite becomes a plain single-column `project_id` index — fewer, cheaper indexes to maintain. |
| **IDs at scale** | `BIGINT UNSIGNED` everywhere (already your convention) — never `INT`, you'll cross 2.1B rows in `activity_logs` faster than you think across 30+ years of daily audit events. |
| **File storage** | Never store files in the DB. `file_path` = object storage key. This is the single biggest scale lever — DB stays small (metadata only), files scale independently on S3/GCS with their own lifecycle/tiering. |
| **Read-heavy dashboards** | Keep D6 (derived, not stored) for live counts, but add one **materialized summary table** fed by a nightly job for historical trend charts (`project_status_daily_snapshot`: date, module_id, status_id, count) — avoids scanning millions of historical rows every time someone opens a 5-year trend chart. |
| **Read replica** | Once `projects`/`project_documents` pass a few million rows, point the dashboard/report queries at a read replica so heavy analytical queries never contend with the transactional wizard writes. |
| **Connection/query hygiene** | Always filter by `project_id` (indexed) before status/date, never scan `activity_logs` by `created_at` alone without a `project_id` — that's why `idx_log_project_created (project_id, created_at)` is a composite, not two separate indexes. |

---

## 7. Foreign key discipline (applies to every table above)

- **`ON DELETE RESTRICT`** for anything that would silently orphan business-critical history (module_id, status_id, customer references from projects).
- **`ON DELETE SET NULL`** for optional/soft associations (reviewer, uploader, district on a customer).
- **`ON DELETE CASCADE`** only for true "owned children" that have no meaning without the parent (`lease_applications` → `projects`, `project_documents` → `projects`, `project_links` → `projects`).
- **`ON UPDATE CASCADE`** everywhere (standard, cheap, keeps things consistent if a surrogate key ever needs to change — rare with auto-increment PKs but free to specify).
- Every FK column gets an index (already your original principle — carried forward).

---

## 8. Migration path from your current v1 design

Since your `12-migration-plan.md` hasn't been executed yet (per the master doc: *"No migrations created until this architecture is approved"*), this is a **clean insert**, not a retrofit:

1. Build `statuses`, `projects`, `project_links` first (new tables).
2. Build each module table with `project_id` as PK/FK instead of its own `id` (drop the old `customer_id`, `district_id`, `mineral_id`, `status` columns that now live on `projects` — keep module-specific columns only).
3. Build `project_documents`, `project_flows`, `activity_logs` with real `project_id` FKs (delete the `*_type`/`*_id` polymorphic columns entirely).
4. Everything else (`users`, `roles`, `permissions`, `districts`, `minerals`, `lease_categories`, `plan_types`, `applicant_types`, `modules`, `folders`, `document_fields`, `customers`, `mimas_credentials`, inventory tables) is **unchanged** from your original `06-table-design.md`.

This resolves 16.1 items **#5 (cross-module linkage)** and **#12 (status consistency)** as a side effect of the structural fix, and gives you a firm answer to item **#1 (customer model)**: yes, `customers` stays a standalone directory, referenced once from `projects` instead of duplicated across 7 tables.

Items **#6 (inventory scope)**, **#10 (MIMAS encryption — unchanged, still recommended)**, and **#7/#8/#13** are unaffected by this change and still need business confirmation as originally flagged.

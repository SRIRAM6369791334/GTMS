# 16 - Unknown Business Requirements (Gaps to Confirm)

Open questions discovered during analysis that **must** be confirmed with the business before the architecture is locked and migrations are written.

---

## 16.1 Confirmation Checklist

### 1. Customer vs. Application identity model 🔴
- Is the **Customer a standalone directory entity** (own CRUD) that all applications reference via `customer_id`?
- OR are customers only captured inline inside each wizard (no shared master)?
- Decision affects whether we create a `customers` table at all, and whether project tables carry `customer_id` or denormalized `client_name` snapshots.
- **Impact:** core design (05.3.1, 06.3).

### 2. District code / master 🟡
- Should `districts` be a centrally-maintained master table (recommended) or free text per record?
- If free text today, is a future master migration acceptable?

### 3. Folders: global master vs per-project 🟡
- Are the folder sets **fixed per module** (derive from a `folders` master keyed by `module_id`) or can each **project define its own folders** (needs a `project_folders` table)?
- Impact: 06.4 (folders + project_documents).

### 4. Document checklist: master vs per-project 🟡
- Are module checklists (16-item lease, 29-item environment, etc.) **static masters** (`document_fields`) or user-defined per project?
- For a new project, should all checklist rows be pre-created, or created only for documents actually uploaded?

### 5. Cross-module linkage 🔴
- Are the modules **independent silos**, or is there an **overarching master application/lease** that mining, EC, PPT, surveys, EC-certificate all attach to?
- Should `ec_certificates` link to `environment_projects`?
- Should surveys reference a lease/mining application?

### 6. Inventory (unit/category/product/product stock) scope 🔴
- The inventory master appears unrelated to mining workflow. **Is it in scope** for this system, or leftover/demo?
- If in scope, does stock need transactional history (sales) or is the static qty sufficient?

### 7. Exact permission keys 🔵→🟡
- Sidebar references gates like `branch.view`, `roles.view`, `users.view`, `application.view`, `mining.view`, `environment.view`, `ppt.view`, `dgps.view`, `drone.view`.
- Need the **complete/authoritative permission list** (from `routes/web.php` middleware) to seed `permissions`.

### 8. User role/branch semantics 🟡
- Is `<department>` the same entity as `<branch>` (used in user/product forms)? Confirm the canonical name.
- Is `show_password` (plaintext hint) intentional/kept?

### 9. Mining workflow stage / report numbers 🟡
- The 6.1–6.6 "process" steps: what defines a **valid** vs **rejected** state per step?
- Are GTM/GTMS **report numbers** required columns or derived from linked applications?

### 10. MIMAS credential handling 🔴
- Confirm MIMAS user_id/password should be **encrypted at rest** and not logged/exported.

### 11. Dashboards / KPI definitions 🔵→🟡
- What exact KPI cards are required (active applications, pending actions by district, etc.)?
- Confirm they are **derived queries** (recommended), not stored.

### 12. Status enumerations 🔵→🟡
- Confirm the exact **project status** set (draft/validation/approved/reported/archived) and **document status** set (pending/uploaded/validated/approved/revision_required) — currently taken from the B2 working backend; verify other modules use the same.

### 13. Notification channels 🟡
- Are in-app notifications needed (DB channel) in addition to activity log? Who gets notified on document review/status change?

---

## 16.2 Prioritization
| Priority | Item | Blocks |
| -------- | ---- | ------ |
| 🔴 High | 1 customer model | customers + all project FKs |
| 🔴 High | 5 cross-module linkage | ec_certificates/env link, surveys |
| 🔴 High | 10 MIMAS encryption | schema/security |
| 🔴 High | 6 inventory scope | whether inventory tables kept |
| 🟡 Medium | 2, 3, 4 master design | folders/document_fields schema |
| 🟡 Medium | 7, 8 permissions | seeders |
| 🟡 Medium | 11, 12 statuses | seeders + display |
| 🟢 Low | 9, 13 | workflow details, notifications |

---

## 16.3 Recommendation to proceed
- Adopt the design in this package as the recommended baseline; it is 3NF and covers all UI-documented behavior.
- Resolve 🔴 items 1, 5, 6, 10 before writing migrations.
- Items 🟡 can be designed in (as masters) and refined during implementation.

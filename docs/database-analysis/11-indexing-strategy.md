# 11 - Indexing Strategy

Indexes are proposed to keep the most common queries fast — list pages, folder/document queries, workflow lookups, and role-permission checks.

---

## 11.1 Principles
- **Every FK gets an index** (unless already covered by a composite).
- **Polymorphic columns** get a composite `(parent_type, parent_id)`.
- **Filter/status columns** get indexes only where the query volume justifies (status is low-cardinality — index is still useful as a leading column with other filters).
- Avoid over-indexing low-cardinality columns alone.

---

## 11.2 Index List

| Table | Index / Columns | Type | Purpose |
| ----- | ---------------- | ---- | ------- |
| users | UQ (email) | unique | login |
| users | role_id | index | list by role |
| users | branch_id | index | list by branch |
| role_permission | UQ (role_id, permission_id) | unique | permission lookup |
| role_permission | permission_id | index | reverse lookup |
| customers | district_id | index | filter by district |
| customers | mineral_id | index | filter by mineral |
| customers | mobile_num | index | duplicate check |
| lease_applications | application_no | unique | lookup |
| lease_applications | customer_id | index | by customer |
| lease_applications | district_id | index | by district |
| lease_applications | status | index | dashboard counts |
| mining_applications | customer_id | index | by customer |
| mining_applications | district_id | index | by district |
| mining_applications | mineral_id | index | by mineral |
| mining_applications | stage | index | dashboards |
| environment_projects | status | index | B2 list + status filter |
| environment_projects | project_code | unique | lookup |
| environment_projects | district_id | index | filter |
| ec_certificates | ec_ref_no | unique | lookup |
| ec_certificates | environment_project_id | index | join |
| ppt_applications | application_no | unique | lookup |
| ppt_applications | customer_id | index | by customer |
| dgps_surveys | survey_no | unique | lookup |
| dgps_surveys | customer_id | index | by customer |
| drone_surveys | survey_no | unique | lookup |
| drone_surveys | customer_id | index | by customer |
| **project_documents** | **(documentable_type, documentable_id)** | index | **primary lookup per project** |
| project_documents | folder_id | index | per-folder queries |
| project_documents | (documentable_type, documentable_id, folder_id) | index | folder progress |
| project_documents | document_field_id | index | checklist references |
| project_documents | status | index | validation filtering |
| **project_flows** | **(flowable_type, flowable_id)** | index | step lookup |
| project_flows | UQ (flowable_type, flowable_id, step_code) | unique | one status per step |
| **activity_logs** | **(loggable_type, loggable_id)** | index | per-entity activity |
| activity_logs | user_id | index | user actions |
| activity_logs | created_at | index | recent activity / feed |
| folders | UQ (module_id, name) | unique | master integrity |
| document_fields | folder_id | index | checklist per folder |
| districts | UQ (name) | unique | |
| minerals | UQ (name) | unique | |
| lease_categories | UQ (code) | unique | |
| products | branch_id | index | inventory |
| products | cat_id | index | inventory |

---

## 11.3 Composite Query Patterns

1. **Dashboard district counts:** `COUNT(*) FROM mining_applications WHERE district_id=? GROUP BY status` → index (district_id), (status).
2. **Project docs progress:** `SELECT status, COUNT(*) FROM project_documents WHERE documentable_type=? AND documentable_id=? GROUP BY status` → index (documentable_type, documentable_id).
3. **Per-folder progress:** adds `AND folder_id=?` → index (documentable_type, documentable_id, folder_id).
4. **Recent activity feed:** `WHERE loggable_type=? AND loggable_id=? ORDER BY created_at DESC` → index (loggable_type, loggable_id, created_at).
5. **Role permission load:** `JOIN role_permission ON ... WHERE role_id=?` → UQ (role_id, permission_id).

---

## 11.4 Notes
- Use `InnoDB` (default in Laravel/MySQL) for FK support + transactions.
- `deleted_at` (soft delete) columns should be added to the composite indexes where soft-delete filtering matters, or rely on partial filtering — **VERIFY performance** if tables grow large.
- UUID primary keys for `project_documents`/`activity_logs` optional; auto-increment `bigint` recommended for simplicity.

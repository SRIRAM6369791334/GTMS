# 15 - Data Dictionary

Consolidated glossary of every table and notable column used in the proposed design.

---

## 15.1 Tables

| Table | Description | Category |
| ----- | ----------- | -------- |
| users | System users (auth) | Identity |
| departments | Departments / branches (District Mining Office units) | Identity |
| roles | Access roles | Identity |
| permissions | Granular permission keys | Identity |
| role_permission | M:N role↔permission pivot | Identity |
| password_resets | Framework auth resets | Framework |
| personal_access_tokens | Sanctum tokens | Framework |
| districts | TN districts lookup | Master |
| minerals | Mineral types lookup | Master |
| lease_categories | Category under rule for lease | Master |
| plan_types | Mining plan types | Master |
| applicant_types | Applicant legal types | Master |
| modules | Application modules (mining, lease, env, ppt, dgps, drone, ec) | Master |
| folders | Folder sets per module | Master |
| document_fields | Document checklist master per folder | Master |
| customers | Customer / company directory | Core |
| lease_applications | Lease application projects | Core |
| mimas_credentials | MIMAS login (1:1 lease) | Core |
| mining_applications | Mining plan applications | Core |
| environment_projects | EC B1/B2 projects | Core |
| ec_certificates | EC certificates issued | Core |
| ppt_applications | PPT department applications | Core |
| dgps_surveys | DGPS survey requests | Core |
| drone_surveys | Drone survey requests | Core |
| project_documents | Polymorphic uploaded documents (all modules) | Cross-cutting |
| project_flows | Polymorphic workflow/validation steps | Cross-cutting |
| activity_logs | Polymorphic audit trail | Cross-cutting |
| notifications | Framework notification feed | Framework |
| units | Inventory units | Inventory |
| product_categories | Inventory categories | Inventory |
| products | Inventory products | Inventory |

---

## 15.2 Notable Columns

| Column | Type | Meaning |
| ------ | ---- | ------- |
| id | bigint/unsigned | PK (all tables) |
| documentable_type/id | string/bigint | polymorphic parent (project_documents) |
| flowable_type/id | string/bigint | polymorphic parent (project_flows) |
| loggable_type/id | string/bigint | polymorphic parent (activity_logs) |
| status (projects) | string(50) | draft/validation/approved/reported/archived |
| status (documents) | string(30) | pending/uploaded/validated/approved/revision_required |
| review_note | text | reject/revision reason |
| reviewed_by / reviewed_at | bigint/timestamp | reviewer audit |
| uploaded_by / uploaded_at | bigint/timestamp | uploader audit |
| file_path / file_name / file_type / file_size | string/bigint | storage metadata |
| created_by | bigint | authorship (optional) |
| show_password | string(255) | user hint (legacy) |

---

## 15.3 Status Value Sets

| Domain | Allowed Values |
| ------ | -------------- |
| Project status | draft, validation, approved, reported, archived |
| Document status | pending, uploaded, validated, approved, revision_required |
| User / master status | 1 (enabled), 0 (disabled) |
| EC communication | grant, rejection |
| Environment category | B1, B2 |
| Folder progress | computed (uploaded vs total) |

---

## 15.4 Naming / format rules
- All timestamps: `created_at`, `updated_at`, `deleted_at` (where soft-delete).
- Money/area: `decimal` (10,2) or (12,2) for GST/MRP.
- Phone/codes: `string` (leading zeros must be preserved — never integer).
- Mobile format: 10-digit Indian mobile, validated client + server.

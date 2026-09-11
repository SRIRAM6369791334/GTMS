# 06 - Table Design (3NF Database Schema)

This document defines every table in the proposed database, its columns, data types, keys, and confidence level. All tables are in **3NF**.

**Confidence legend:** 🔵 CONFIRMED FROM UI · 🟢 INFERRED FROM UI STRUCTURE · 🟡 NEEDS BUSINESS CONFIRMATION

---

## 6.1 Identity & Access

### `users` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| name | string(255) | no | | |
| email | string(255) | no | UQ | |
| mobile_num | string(15) | yes | | |
| role_id | unsignedBigInteger | no | FK→roles | |
| branch_id | unsignedBigInteger | yes | FK→branches | nullable |
| image | string(255) | yes | | file path |
| email_verified_at | timestamp | yes | | |
| password | string(255) | no | | bcrypt |
| show_password | string(255) | yes | | displayed/hint password |
| remember_token | string(100) | yes | | |
| status | tinyInteger | no | | default 1 |
| created_at / updated_at | timestamp | yes | | |

### `departments` (aka `branches`) 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| branch_name | string(255) | no | UQ | |
| contact_person | string(255) | no | | |
| mobile | string(15) | no | | |
| address | text | no | | |
| city | string(255) | yes | | |
| state | string(255) | yes | | |
| pincode | string(10) | yes | | |
| status | tinyInteger | no | | default 1 |
| created_at / updated_at | timestamp | yes | | |

### `roles` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| name | string(255) | no | UQ | |
| description | string(255) | yes | | |
| created_at / updated_at | timestamp | yes | | |

### `permissions` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| module | string(100) | no | | menu module |
| name | string(100) | no | UQ | permission key, e.g. `users.view` |
| display_name | string(100) | no | | |
| created_at / updated_at | timestamp | yes | | |

### `role_permission` (pivot) 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| role_id | unsignedBigInteger | no | FK→roles | |
| permission_id | unsignedBigInteger | no | FK→permissions | |
| created_at / updated_at | timestamp | yes | | |
| **UQ:** (role_id, permission_id) | | | | |

### `password_resets` 🟢 (framework)
`email`, `token`, `created_at`.

### `personal_access_tokens` 🟢 (framework, Sanctum)
`id`, `tokenable` (morph), `name`, `token`, `abilities`, `last_used_at`, `expires_at`.

---

## 6.2 Master / Lookup

### `districts` 🟡
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| name | string(100) | no | UQ | |
| state | string(100) | yes | | default Tamil Nadu |
| status | tinyInteger | no | | |
| created_at / updated_at | timestamp | yes | | |

### `minerals` 🟡
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| name | string(100) | no | UQ | Rough Stone, Gravel, Granite, Lime Stone, Fire Clay, Others |
| status | tinyInteger | no | | |
| created_at / updated_at | timestamp | yes | | |

### `lease_categories` 🟡
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| code | string(20) | no | UQ | MDCC, Rule 12, 19(1), 19(2)(a), 19-A, 36-F, 44, 7 |
| name | string(255) | no | | category under rule |
| status | tinyInteger | no | | |

### `plan_types` 🟡
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| name | string(100) | no | UQ | Mining Plan, Revised, Modified, Scheme |
| status | tinyInteger | no | | |

### `applicant_types` 🟡
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| name | string(100) | no | UQ | Individual, Partnership, Pvt Ltd, Trust |
| status | tinyInteger | no | | |

### `modules` 🟢
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| code | string(50) | no | UQ | mining, lease, environment, ppt, dgps, drone, ec |
| name | string(100) | no | | |
| status | tinyInteger | no | | |

**Decision:** Master lists are seeded and managed by admins. Creating these as tables (rather than columns) avoids repeating string values across thousands of application rows.

---

## 6.3 Core Business / Project Entities

### `customers` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| customer_name | string(255) | no | | representative name |
| company_name | string(255) | no | | |
| mobile_num | string(15) | no | | |
| email | string(255) | yes | | |
| district_id | unsignedBigInteger | yes | FK→districts | |
| mineral_id | unsignedBigInteger | yes | FK→minerals | |
| gstin | string(15) | yes | | |
| pan | string(10) | yes | | |
| area | decimal(10,2) | yes | | quarry area Ha |
| address | text | yes | | |
| status | tinyInteger | no | | |
| created_by | unsignedBigInteger | yes | FK→users | |
| created_at / updated_at | timestamp | yes | | |

**Note on 3NF:** district & mineral are pulled out into FK tables to eliminate the repeated string columns that appeared in both customer and every application form.

### `lease_applications` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| customer_id | unsignedBigInteger | yes | FK→customers | |
| application_no | string(50) | no | UQ | |
| district_id | unsignedBigInteger | yes | FK→districts | |
| category_id | unsignedBigInteger | yes | FK→lease_categories | |
| contact_person | string(255) | yes | | |
| contact_mobile | string(15) | yes | | |
| survey_no | string(100) | yes | | |
| taluk | string(255) | yes | | |
| village | string(255) | yes | | |
| area_extent | decimal(10,2) | yes | | |
| mineral_id | unsignedBigInteger | yes | FK→minerals | |
| lease_period | integer | yes | | months/years 🟡 |
| status | string(50) | no | | draft/submitted/… 🟡 |
| created_by | unsignedBigInteger | yes | FK→users | |
| created_at / updated_at | timestamp | yes | | |

### `mimas_credentials` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| lease_application_id | unsignedBigInteger | no | FK→lease_applications | 1:1 |
| user_id | string(255) | no | | ENCRYPTED |
| password | string(255) | no | | ENCRYPTED |
| email | string(255) | no | | |
| contact_number | string(15) | no | | |
| created_at / updated_at | timestamp | yes | | |

### `mining_applications` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| customer_id | unsignedBigInteger | yes | FK→customers | |
| applicant_type_id | unsignedBigInteger | yes | FK→applicant_types | |
| district_id | unsignedBigInteger | yes | FK→districts | |
| taluk | string(255) | yes | | |
| village | string(255) | yes | | |
| survey_number | string(255) | yes | | |
| mineral_id | unsignedBigInteger | yes | FK→minerals | |
| plan_type_id | unsignedBigInteger | yes | FK→plan_types | |
| stage | string(50) | yes | | validation stage 🟡 |
| report_no | string(50) | yes | | GTM/GTMS report 🟡 |
| client_name | string(255) | yes | | snapshot (denormalized) 🟡 |
| created_by | unsignedBigInteger | yes | FK→users | |
| created_at / updated_at | timestamp | yes | | |

### `environment_projects` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| project_code | string(50) | yes | UQ | |
| client_name | string(255) | yes | | |
| project_name | string(255) | no | | |
| location | string(255) | yes | | |
| district_id | unsignedBigInteger | yes | FK→districts | |
| category | string(20) | yes | | B1 / B2 🟡 |
| contact_name | string(255) | yes | | |
| contact_phone | string(20) | yes | | |
| contact_email | string(255) | yes | | |
| status | string(50) | no | default 'draft' | draft/validation/approved/reported/archived |
| created_by | unsignedBigInteger | yes | FK→users | |
| created_at / updated_at | timestamp | yes | | |

### `ec_certificates` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| ec_ref_no | string(50) | no | UQ | |
| environment_project_id | unsignedBigInteger | yes | FK→environment_projects | linkage 🟡 |
| parivesh_app_no | string(50) | yes | | |
| applicant_name | string(255) | no | | |
| approval_date | date | yes | | |
| certificate_file | string(255) | yes | | file path |
| communication_type | string(20) | yes | | Grant / Rejection |
| recipient_email | string(255) | yes | | |
| communication_note | text | yes | | |
| status | string(50) | yes | | 🟡 |
| created_by | unsignedBigInteger | yes | FK→users | |
| created_at / updated_at | timestamp | yes | | |

### `ppt_applications` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| application_no | string(50) | no | UQ | |
| customer_id | unsignedBigInteger | yes | FK→customers | |
| project_name | string(255) | yes | | |
| district_id | unsignedBigInteger | yes | FK→districts | |
| taluk_village | string(255) | yes | | |
| mineral_id | unsignedBigInteger | yes | FK→minerals | |
| status | string(50) | yes | | 🟡 |
| created_by | unsignedBigInteger | yes | FK→users | |
| created_at / updated_at | timestamp | yes | | |

### `dgps_surveys` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| survey_no | string(50) | no | UQ | |
| customer_id | unsignedBigInteger | yes | FK→customers | |
| lease_area | decimal(10,2) | yes | | |
| location | string(255) | yes | | |
| survey_date | date | yes | | |
| survey_team | string(255) | yes | | |
| survey_status | string(50) | yes | | 🟡 |
| report_status | string(50) | yes | | 🟡 |
| gtm_report_file | string(255) | yes | | |
| remarks | text | yes | | |
| created_by | unsignedBigInteger | yes | FK→users | |
| created_at / updated_at | timestamp | yes | | |

### `drone_surveys` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| survey_no | string(50) | no | UQ | |
| customer_id | unsignedBigInteger | yes | FK→customers | |
| lease_area | decimal(10,2) | yes | | |
| location | string(255) | yes | | |
| flight_date | date | yes | | |
| drone_pilot | string(255) | yes | | |
| survey_status | string(50) | yes | | 🟡 |
| deliverable_file | string(255) | yes | | orthomosaic/contour/3D/report |
| gtms_report_file | string(255) | yes | | |
| created_by | unsignedBigInteger | yes | FK→users | |
| created_at / updated_at | timestamp | yes | | |

---

## 6.4 Polymorphic / Cross-cutting

### `project_documents` 🔵 (core shared table)
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| documentable_type | string(100) | no | | e.g. App\Models\EnvironmentProject |
| documentable_id | unsignedBigInteger | no | | polymorphic |
| folder_id | unsignedBigInteger | yes | FK→folders | |
| document_field_id | unsignedBigInteger | yes | FK→document_fields | checklist master ref |
| file_name | string(255) | yes | | original name |
| file_path | string(255) | yes | | stored path |
| file_type | string(10) | yes | | extension |
| file_size | bigInteger | yes | | bytes |
| status | string(30) | yes | default 'pending' | pending/uploaded/validated/approved/revision_required |
| review_note | text | yes | | from review modal |
| reviewed_by | unsignedBigInteger | yes | FK→users | |
| reviewed_at | timestamp | yes | | |
| uploaded_by | unsignedBigInteger | yes | FK→users | |
| uploaded_at | timestamp | yes | | |
| created_at / updated_at | timestamp | yes | | |
| **IX** indexes | | | | (documentable_type, documentable_id), folder_id, document_field_id |

### `folders` 🟡
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| module_id | unsignedBigInteger | yes | FK→modules | |
| name | string(100) | no | | |
| sort_order | integer | yes | | |
| status | tinyInteger | no | | |
| created_at / updated_at | timestamp | yes | | |
| **UQ:** (module_id, name) | | | | |

> **Decision:** Folders are a **global master** discriminated by module (mining/lec/env/ppt…). Each project's documents reference `folder_id`; a dedicated `project_folders` table is only needed if folder sets vary per project (see 05.4 / 16).

### `document_fields` 🟡 (checklist master)
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| folder_id | unsignedBigInteger | no | FK→folders | |
| name | string(255) | no | | the required doc label |
| required | boolean | no | default true | |
| sort_order | integer | yes | | |
| status | tinyInteger | no | | |
| created_at / updated_at | timestamp | yes | | |

### `project_flows` (validation stages) 🟢
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| flowable_type | string(100) | no | | polymorphic |
| flowable_id | unsignedBigInteger | no | | |
| step_code | string(20) | no | | e.g. 6.1–6.6, EC step |
| step_name | string(100) | no | | Upload & Store, Validate, … |
| status | string(30) | no | default 'pending' | pending/in_progress/passed/rejected |
| note | text | yes | | |
| handled_by | unsignedBigInteger | yes | FK→users | |
| handled_at | timestamp | yes | | |
| created_at / updated_at | timestamp | yes | | |
| **UQ:** (flowable_type, flowable_id, step_code) | | | | |

### `activity_logs` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | bigInteger | no | PK | |
| loggable_type | string(100) | yes | | polymorphic |
| loggable_id | unsignedBigInteger | yes | | |
| user_id | unsignedBigInteger | yes | FK→users | |
| action | string(100) | no | | e.g. status_changed, doc_uploaded |
| description | text | yes | | |
| properties | json | yes | | extra context |
| created_at | timestamp | yes | | |

### `notifications` 🟢 (framework DB channel)
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | uuid | no | PK | |
| type | string(255) | no | | |
| notifiable_type / notifiable_id | morph | no | | |
| data | json | no | | |
| read_at | timestamp | yes | | |

---

## 6.5 Inventory (out of core mining scope, flagged)

### `units` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| units | string(100) | no | UQ | |
| created_at / updated_at | timestamp | yes | | |

### `product_categories` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| cat_code | string(50) | no | UQ | |
| cat_name | string(255) | no | UQ | |
| created_at / updated_at | timestamp | yes | | |

### `products` 🔵
| Column | Type | Null | Key | Notes |
| ------ | ---- | ---- | --- | ----- |
| id | unsignedBigInteger | no | PK | |
| branch_id | unsignedBigInteger | no | FK→departments | |
| cat_id | unsignedBigInteger | no | FK→product_categories | |
| pro_name | string(255) | no | | |
| gst | decimal(5,2) | no | | |
| cast_per | decimal(10,2) | no | | |
| mrp | decimal(10,2) | no | | |
| qty | integer | no | | |
| discount_1 | decimal(10,2) | yes | | Discount R |
| discount_2 | decimal(10,2) | yes | | Discount G |
| discount_3 | decimal(10,2) | yes | | Discount Y |
| created_at / updated_at | timestamp | yes | | |

---

## 6.6 Naming & Conventions
- Tables: lowercase, snake_case, plural.
- FK: `<singular>_id`.
- Polymorphic columns: `<parent>_type` / `<parent>_id`.
- Timestamps + soft deletes (`deleted_at`) where appropriate. **RECOMMEND soft deletes** on customers, all project tables, documents.
- Encrypt sensitive columns (MIMAS) via Laravel model accessors.

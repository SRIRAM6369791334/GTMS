# 07 - Relationship Diagram / ERD (Text)

This document describes all relationships between tables as a text-based ERD. Read A→B as "one A has many B" unless marked 1:1.

**Legend:** 🔵 CONFIRMED · 🟢 INFERRED · 🟡 NEEDS CONFIRMATION

---

## 7.1 User & Access

```
roles 1───* role_permission *───1 permissions
roles 1───* users
departments 1───* users
users 1───* (created_by on projects, documents, reviews, activity)
```

| Relationship | Cardinality | Type | Evidence |
| ------------ | ----------- | ---- | -------- |
| users ↔ roles | * : 1 | FK users.role_id → roles.id | 🔵 user form "Role" select |
| users ↔ departments | * : 1 | FK users.branch_id → departments.id | 🔵 "Branch" select, UserController |
| roles ↔ permissions | * : * | pivot role_permission | 🔵 permission checkboxes |

---

## 7.2 Master lookups

```
modules 1───* folders 1───* document_fields
districts 1───* customers
minerals 1───* customers
```

---

## 7.3 Customers → Projects

All project types share a **party** relationship to customers.

```
customers 1───* lease_applications
customers 1───* mining_applications
customers 1───* environment_projects    (via client snapshot 🟡)
customers 1───* ec_certificates         (via applicant 🟡)
customers 1───* ppt_applications
customers 1───* dgps_surveys
customers 1───* drone_surveys
```

| From | To | Cardinality | Type | Evidence |
| ---- | -- | ----------- | ---- | -------- |
| customers | each project | 1 : * | FK customer_id (nullable) | 🔵 / 🟡 |

---

## 7.4 Polymorphic document structure (the key design)

```
                    ┌─────────────┐
modules 1───* folders 1───* document_fields
                    │
                    │  (folder_id, document_field_id)
                    ▼
        project_documents  ◄── polymorphic: documentable_type/id
                    ▲
                    │  points to any of:
                    │  lease_applications, mining_applications,
                    │  environment_projects, ec_certificates,
                    │  ppt_applications, dgps_surveys, drone_surveys
                    │
project_documents *───1 users (uploaded_by, reviewed_by)
```

**Single `project_documents` table replaces the 7 hypothetical per-module document tables.**

---

## 7.5 Workflow / Lifecycle (polymorphic)

```
                    ┌─────────────┐
                    │ project_flows │  ◄ polymorphic flowable_type/id (step 6.1–6.6 etc.)
                    └─────────────┘
                    ▲
                    │
        project_documents *───1 project (documentable)
        activity_logs *───1 project (loggable) , *───1 users
```

- **project_flows** tracks validation/EC/PPT steps per project polymorphically.
- **activity_logs** records every action against any entity and user.

---

## 7.6 Domain-specific relationships

| From | To | Cardinality | Type | Evidence |
| ---- | -- | ----------- | ---- | -------- |
| lease_applications | mimas_credentials | 1 : 1 | FK lease_application_id UQ | 🔵 step 6 |
| lease_applications | lease_categories | * : 1 | FK category_id | 🔵 step 3 |
| lease_applications | districts | * : 1 | FK district_id | 🔵 step 1 |
| lease_applications | minerals | * : 1 | FK mineral_id | 🟡 |
| mining_applications | applicant_types | * : 1 | FK applicant_type_id | 🟡 |
| mining_applications | plan_types | * : 1 | FK plan_type_id | 🟡 |
| mining_applications | districts | * : 1 | FK district_id | 🔵 |
| environment_projects | districts | * : 1 | FK district_id | 🟢 |
| ec_certificates | environment_projects | * : 1 | FK environment_project_id | 🟡 |
| ppt_applications | districts/minerals | * : 1 | FK | 🔵/🟡 |
| dgps_surveys | customers | * : 1 | FK customer_id | 🟢 |
| drone_surveys | customers | * : 1 | FK customer_id | 🟢 |

---

## 7.7 Inventory (out of core scope)

```
departments 1───* products
product_categories 1───* products
```

---

## 7.8 Recursive / self-references
- **users.id = created_by** on most entities (audit authorship) — convention, not enforced FK in schema unless desired.

---

## 7.9 Summary — complete relationship count
| Type | Count |
| ---- | ----- |
| 1 : 1 | 1 (lease↔mimas) |
| 1 : * | 18 |
| * : * | 1 (roles↔permissions) |
| polymorphic | 3 (project_documents, project_flows, activity_logs) |
| total tables | ~30 |

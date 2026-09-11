# 05 - Entity Discovery & Normalization

This document walks through the process of extracting **candidate entities** from the UI, grouping repeated/related fields into atomic tables, and applying **3NF normalization** to eliminate redundancy.

---

## 5.1 Discovery Method

1. **Do NOT** blindly turn each page into a table.
2. Scan for **repeated field groups** (e.g., client name + mobile + email appearing in Customers, Lease, Mining, PPT, EC, DGPS, Drone).
3. Isolate **repeated multi-valued structures** (folders + document checklists appearing identically across 6+ modules) → these are **polymorphic relationships**, not columns.
4. Identify **master/lookup data** (district, mineral, category, plan type, folder, document field).
5. Flag **aggregates** as derived (not stored).

---

## 5.2 Candidate Entity List

### A. Identity / Security
| Entity | Source | Notes |
| ------ | ------ | ----- |
| **users** | user.blade | role_id, branch_id, mobile_num, image, email, password, status |
| **departments** (branch) | branch | branch_name, contact_person, mobile, address, city, state, pincode, status |
| **roles** | role | name, description |
| **permissions** | role (checkboxes) | permission keys |
| **role_permission** | role | M:N pivot |

### B. Master / Lookup
| Entity | Source | Notes |
| ------ | ------ | ----- |
| **districts** | dropdowns | master (pending confirm) |
| **minerals** | radio cards | master (pending confirm) |
| **categories** (lease rule) | lease step 3 | master (pending confirm) |
| **plan_types** | mining/ppt | master (pending confirm) |
| **applicant_types** | mining step 1 | master (pending confirm) |
| **units** | master/unit | **CONFIRMED FROM UI** working |
| **categories** (product) | master/category | **CONFIRMED FROM UI** working |
| **products** | master/product | **CONFIRMED FROM UI** working |
| **branches** | product form | same as departments |

### C. Core Business / Project Entities
| Entity | Source | Notes |
| ------ | ------ | ----- |
| **customers** | customers.blade | standalone directory (customer_name, company_name, mobile, email, district, mineral, gstin, pan, area, address, status) |
| **lease_applications** | lease_application | wizard + MIMAS + category |
| **mining_applications** | mining-portal | wizard intake + folders |
| **env_projects** (B1/B2) | enviro_b1/b2 | project + status + flow |
| **ec_certificates** | ec_certificate | issuance |
| **ppt_applications** | ppt_department | 7-step + 11 folders |
| **dgps_surveys** | dgps_survey | 6-step |
| **drone_surveys** | drone_survey | 6-step |

### D. Polymorphic / Shared Structures (unique insight)
| Entity | Source | Notes |
| ------ | ------ | ----- |
| **project_folders** | all wizard modules | polymorphic folder sets per project |
| **project_documents** | all wizard modules | polymorphic uploaded files per folder |
| **document_fields** (checklist master) | all checklists | the list of required docs per folder |
| **project_flows** / **activity_log** | process, enviro_b2 show | lifecycle + audit trail |

---

## 5.3 Normalization Analysis (to 3NF)

### 5.3.1 Customer + Application (the key modeling question)
- Customers share fields: name, company, mobile, email, district, mineral, GSTIN, PAN.
- Applications reference a client but also allow ad-hoc client data entry (lease step 1 is just "Client Name", mining step 1 is full applicant fields).
- **Decision:** Introduce a **customers** table as the canonical party record. Applications reference `customer_id`. Where the wizard enters client-only data without full registration, create the customer row on the fly (or keep denormalized snapshot fields). **RECOMMENDED:** reference `customer_id` FK on all project tables, plus keep denormalized `client_name` for report snapshots. **NEEDS BUSINESS CONFIRMATION.**

### 5.3.2 Folders + Documents (polymorphism)
- Folders appear across 6 modules, **each module has a different folder set**:
  - Mining: Field Log, Documents, Site Photos, Report, Plan, Others
  - Environment B2: Documents, Site Photographs, Report, GIS, Signed Reports, PARIVESH
  - PPT: Documents, EDS & EDS Reply, Demand Note, File No, SEAC Agenda, SEAC Minutes, ADS, CER Affidavit, SEIAA Agenda, SEIAA Minutes, EC
  - Lease: Documents, Lease Application, Plan
- Do NOT repeat folder columns per module. Use a **polymorphic `project_documents`** table with `documentable_type` + `documentable_id`, and a `folders`/`document_fields` master keyed to each module.

### 5.3.3 Checklist items as master
- Each module's checklist is a **finite document list**. Model as master `document_fields` with a module/type discriminator + folder, so projects reference `document_field_id` rather than storing document names as strings. **NEEDS BUSINESS CONFIRMATION** (fixed list vs per-project).

### 5.3.4 Product / Stock
- `products` holds attributes (branch, category, name, gst, cast, mrp, qty, discounts).
- Stock is derived. If stock changes via sales are needed, an **inventory_transactions** table is required; otherwise stock is computed. Out of mining scope — **NEEDS BUSINESS CONFIRMATION** whether inventory is part of this system.

### 5.3.5 MIMAS credentials
- Stored as standalone fields on lease application (user_id, password, email, contact). Sensitive → encrypt. Part of lease application (1:1) as `mimas_credentials`.

---

## 5.4 Final Entity Summary (41 entities → consolidated)

### Identity (7)
1. users
2. departments
3. roles
4. permissions
5. role_permission (pivot)
6. password_resets (framework)
7. personal_access_tokens (framework)

### Master / Lookup (10)
8. districts
9. minerals
10. lease_categories
11. plan_types
12. applicant_types
13. units
14. product_categories
15. products
16. branches (alias of departments, or separate)
17. document_fields

### Core Projects (8)
18. customers
19. lease_applications
20. mining_applications
21. environment_projects
22. ec_certificates
23. ppt_applications
24. dgps_surveys
25. drone_surveys

### Polymorphic / Cross-cutting (6)
26. project_folders
27. project_documents
28. mimas_credentials
29. project_flows (validation stages)
30. activity_logs
31. notifications

### Inventory (extra, if in scope)
32. product_stock / inventory

---

## 5.5 Business-Confirmation Gaps (feeding doc 16)
- Customer vs. application identity model.
- District/master lists centralization.
- Folder/document master vs per-project.
- Inventory inclusion.
- EC reference to which upstream application.

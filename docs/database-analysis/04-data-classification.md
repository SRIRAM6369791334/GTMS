# 04 - Data Classification (What Should / Should NOT Be Stored)

This document classifies every piece of data in the UI into **four** categories to drive the table design.

| Classification | Meaning | Storage Decision |
| -------------- | ------- | ---------------- |
| **PERSIST** | Real business data | Must store in a table |
| **MASTER** | Reference/lookup data | Must store as master/lookup tables |
| **DERIVED** | Computed at query time | Do NOT store (compute on read) |
| **TEMP / STATIC** | Demo, cosmetic, transient | Do NOT store |

---

## 4.1 Should Be Stored (PERSIST)

| Data | Entity | Notes |
| ---- | ------ | ----- |
| Department name, contact person, phone, address, city, state, pincode | Department | **CONFIRMED FROM UI** |
| Role name + permission assignment | Role / Permission | **CONFIRMED FROM UI** |
| User name, role, branch, mobile, image, email, password, status | User | **CONFIRMED FROM UI** (UserController stores name, email, password) |
| Customer / company, mobile, email, district, mineral, gstin, pan, area, address, status | Customer | **CONFIRMED FROM UI** |
| Lease application client, district, category, contact, MIMAS details | Lease Application | **CONFIRMED FROM UI** |
| Mining application client, district, mineral, plan type, folders | Mining Application | **CONFIRMED FROM UI** |
| Flow/parent application steps and validation status | Application workflow | **INFERRED FROM UI STRUCTURE** |
| Environment B1/B2 project info, documents, review, status | Environment Project | **CONFIRMED FROM UI** (B2 has working backend) |
| EC certificate issuance data | EC Certificate | **CONFIRMED FROM UI** |
| PPT application + 11 folders/docs | PPT Application | **CONFIRMED FROM UI** |
| DGPS survey request, data, report | DGPS Survey | **CONFIRMED FROM UI** |
| Drone survey request, flight, deliverables | Drone Survey | **CONFIRMED FROM UI** |
| Unit, Category, Product, Product Stock | Inventory master | **CONFIRMED FROM UI** (working CRUD) |

## 4.2 Should Be Stored as Master / Lookup

| Data | Entity | Evidence |
| ---- | ------ | -------- |
| District | District | Dropdowns everywhere (customer, application, mining, PPT). **NEEDS BUSINESS CONFIRMATION** whether it is a fixed master or free-text. |
| Mineral type | Mineral | Radio card list reused across modules (Rough Stone, Gravel, Granite, Lime Stone, Fire Clay, Others). **INFERRED FROM UI STRUCTURE** |
| Lease category under rule | Category | Radio cards in lease step 3 (MDCC, Rule 12, 19, 19-A, 36-F, 44, 7). **INFERRED FROM UI STRUCTURE** |
| Plan type | Plan Type | Radio (Mining Plan, Revised, Modified, Scheme). **INFERRED FROM UI STRUCTURE** |
| Applicant type | Applicant Type | Select (Individual, Partnership, Pvt Ltd, Trust) **INFERRED FROM UI STRUCTURE** |
| Folder types | Folder | Repeated concept across modules with different folder sets. **NEEDS BUSINESS CONFIRMATION** (global vs per-module). |
| Document checklist items | Document Field | 16-/29-item checklists per module. Master vs per-project. **NEEDS BUSINESS CONFIRMATION** |
| Document statuses (uploaded/validated/approved/revision_required) | Status enum | **CONFIRMED FROM UI** |
| Project statuses (draft/validation/approved/reported/archived) | Status enum | **CONFIRMED FROM UI** |
| Unit | Unit | **CONFIRMED FROM UI** (working CRUD) |
| Category | Category | **CONFIRMED FROM UI** (working CRUD) |

## 4.3 Should NOT Be Stored (DERIVED)

| Data | Source | Why Derived |
| ---- | ------ | ----------- |
| Permissions displayed count per role | role_permissions join | Computed |
| "Survey Requests 31", application counts on dashboard | hardcoded/aggregate | Aggregated from tables at query time |
| Document count per project | document join | Aggregated |
| Total / Available / Sales Stock | product stock | Computed from inventory transactions |
| Folder upload progress % | document status | Computed |
| Recent activity feed | activity log | Derived from activity_log table (log itself stored) |

## 4.4 Should NOT Be Stored (TEMP / STATIC / DEMO)

| Data | Reason |
| ---- | ------ |
| Notification bell demo items | Static demo |
| "Send Message" modal (Author/Email/Comment) | Static, no backend |
| Step wizard progress / active tab | Session/local UI state |
| Global header search keyword | Transient query |
| Confirmation dialogs, toasts, validation messages | Transient UI |
| Chart series / KPI labels | Rendered; underlying data already stored elsewhere |

---

## 4.5 Key Master Data Decisions (flagged for confirmation)

1. **District master** — is a centrally-managed list required, or free text per customer/application?
2. **Folder table** — should folders be a **global master** (with per-module folder sets) or per-project records?
3. **Document checklist** — should each module's checklist be a **master lookup** (`document_fields`) or stored per project on creation?
4. **Mineral / Category / Plan-type** — confirm these are stable finite lists (master tables) vs. editable.
5. **Customer vs. Project identity** — is a Customer a standalone entity or a property of an Application (see 05)?

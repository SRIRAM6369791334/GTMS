# GTMS — Project Overview & Knowledge Base

**Project Name:** Granite / Mining Tracking Management System (GTMS)  
**Domain:** District Mining Office Management & Statutory Regulatory ERP  
**State / Region:** Tamil Nadu, India  
**Target Architecture:** Monolithic Laravel 12 Enterprise Application  
**Authoritative Source:** Codebase Inspection & Empirical Audit  
**Document Number:** `00` of `23`

---

## 1. Executive Summary

The **Granite / Mining Tracking Management System (GTMS)** is an enterprise web application designed to digitize, streamline, and govern the end-to-end statutory compliance lifecycle for mining and quarrying operations in Tamil Nadu, India. 

Quarrying operations (principally dimensional granite, rough stone, gravel, and earth) in Tamil Nadu are subject to strict multi-departmental regulatory regimes spanning:
1. **Department of Geology and Mining (DoGM) / District Collectorate:** Initial lease application grants, statutory document verification, revenue land records (Patta, Adangal, FMB), and seigniorage fees.
2. **State Environmental Impact Assessment Authority (SEIAA) / District Environment Impact Assessment Authority (DEIAA):** Environmental Clearance (EC) under Category B1 (requiring Terms of Reference [ToR], Environmental Impact Assessment [EIA], and public hearings) and Category B2 (cluster/non-cluster simplified clearance).
3. **District Expert Appraisal Committee (DEAC) / PPT Department:** In-person PowerPoint presentations and technical appraisals defending quarry plans, green belt buffers, and water conservation.
4. **Geodetic & Cadastral Survey Authorities:** Differential Global Positioning System (DGPS) ground survey coordinate fixation and Directorate General of Civil Aviation (DGCA) compliant drone topographical surveys.
5. **Ministry of Environment, Forest and Climate Change (MoEFCC) / TNPCB:** Half-yearly post-EC statutory compliance monitoring throughout the operational lifespan of the quarry.

Prior to GTMS, applicants and district consultancy offices managed these sequential workflows through disconnected physical paper files, ad-hoc spreadsheets, and disjointed departmental web portals. GTMS unifies these isolated processes into an integrated, audit-trailed, role-governed ERP centered around an immutable **Universal Common ID (`GTMS-{YEAR}-{SEQUENCE}`)** and an interactive **Customer 360 Dossier**.

---

## 2. Core Business Domains & System Modules

GTMS is organized into **nine core functional modules**:

```
                                 ┌──────────────────────────────────┐
                                 │      CUSTOMER 360 DOSSIER        │
                                 │  Central Entity Hub & Billing    │
                                 └───────────────┬──────────────────┘
                                                 │
         ┌───────────────────┬───────────────────┼───────────────────┬───────────────────┐
         ▼                   ▼                   ▼                   ▼                   ▼
  1. LEASE APPL.      2. MINING PLAN       3. ENV. CLEARANCE   4. PPT DEPT.        5. SURVEYS
  (Steps 1–8)         (Stages 6.1–6.6)     (B1 & B2 Workflows) (ToR & Final Gates) (DGPS & Drone)
         │                   │                   │                   │                   │
         └───────────────────┴───────────────────┼───────────────────┴───────────────────┘
                                                 │
                                                 ▼
                                     6. EC HALF-YEARLY COMPLIANCE
                                     (Period-wise Statutory Audit)
```

### Module 1: Customer Directory & Customer 360 Portal
* **Directory Management:** Comprehensive applicant registry capturing company/quarry name, primary representative, site in-charge contacts, district jurisdiction, PAN, Aadhaar, and GSTIN.
* **Master Identifiers:** Generates SEO-friendly unique URL slugs (`/customers/sri-bala-traders`) and maintains backward-compatible unique master identifiers (`mimas_no`).
* **Universal Search:** Multi-faceted search engine capable of matching raw 12-digit Aadhaar numbers against formatted database columns (`XXXX-XXXX-XXXX`), dual phone numbers, and district jurisdictions.
* **Commercial Billing Engine:** Dynamically aggregates active projects across all modules into unified **Proforma Invoices** and official **Tax Invoices** featuring Indian currency word conversions (Crores, Lakhs) and statutory GST splits (SGST 9% + CGST 9% for intra-state vs IGST 18% for inter-state).

### Module 2: Lease Application Wizard (CustomerController)
* **Intake Wizard:** 8-step structured wizard capturing:
  * *Step 1:* Applicant & quarry entity details.
  * *Step 2:* Village, taluk, SF numbers, extent, and mineral selection (multi-mineral support).
  * *Step 3:* Revenue land classifications (Patta, Poramboke, Government land).
  * *Step 4:* 3 statutory folders (Documents, Lease Application, Plan).
  * *Step 5:* 19 mandatory document uploads with real-time dropzone matching.
  * *Step 6:* Application handlers (assigned officers & legal representatives).
  * *Step 7:* Polymorphic payment fee configuration (product value, paid, pending).
  * *Step 8:* Comprehensive preview, draft persistence, and final submission.
* **Process Flow & Scrutiny:** Stages 6.1 through 6.5 enforcing status transitions (`submitted` → `under_validation` → `validated` → `approved` / `rejected`), atomic document-level scrutiny, compliance dossier PDF generation, and one-click promotion to Mining Plan.

### Module 3: Mining Plan Application (MiningController)
* **Intake & Resumption:** Structured intake capturing nature of work (Fresh Grant, Scheme of Mining, Review of Mining Plan), Recognized Qualified Person (RQP) details, production schedules, and 5-year mineral targets.
* **Cross-Module Handoff:** Carries forward applicant identity, survey numbers, minerals, and cloned statutory revenue documents from approved lease applications.
* **Process Flow Stages:** 6 sequential scrutiny stages (6.1 Scrutiny → 6.2 Field Inspection → 6.3 Technical Review → 6.4 DGM Verification → 6.5 Collectorate Handoff → 6.6 Final Approval) with attached inspection reports.

### Module 4: Environmental Clearance (EnverionsoneController & EnvironmentalB2Controller)
* **Category B1 Sequential 2-Stage Lifecycle:**
  * *Stage 1 (SC1 Preparation):* Intake of 5 ToR document folders. Submission gate to PPT Department (`presentation_stage = 'tor_presentation'`).
  * *Stage 1 PPT Approval Gate:* Unlocks Sub Category 2 (SC2).
  * *Stage 2 (SC2 Preparation):* 6 EIA & TNPCB document folders (EIA study, public hearing minutes, executive summaries).
  * *Stage 2 PPT Approval Gate:* Grants final EC presentation approval.
* **Category B2 Fast-Track Workflow:** 6-folder direct submission wizard for quarries below threshold capacity.

### Module 5: PPT Department (PptDepartmentController)
* **Role:** Manages technical presentations before the State Expert Appraisal Committee (SEAC) / District Appraisal Committee (DEAC).
* **Workflows:** 5-step wizard tracking presentation dates, agenda numbers, committee chairman remarks, presentation file uploads (PPT/PDF), and stage-specific approval gates (`approve-stage`).

### Module 6: DGPS Survey (DgpsSurveyController)
* **Cadastral Geo-Fixation:** 4-step wizard recording ground boundary pillars (A, B, C, D...), latitude/longitude coordinates (degrees, minutes, seconds & decimal degrees), UTM zone projections, raw instrument logs, and composite GeoJSON/KML boundary maps.

### Module 7: Drone Survey (DroneSurveyController)
* **Volumetric & Aerial Audits:** 2-step tracking wizard capturing pilot DGCA credentials, flight dates, raw imagery, orthomosaic GeoTIFFs, 3D point clouds, and computed quarry pit excavation volumes.

### Module 8: EC Half-Yearly Compliance (EcComplianceController)
* **Post-Approval Monitoring:** Tracks ongoing compliance with Specific and General conditions stipulated in the Environmental Clearance order for each 6-month period (April–September, October–March). Manages environmental monitoring test reports (ambient air, noise, ground water, soil analysis).

### Module 9: Access Control & Master Data Management
* **Role-Based Access Control (RBAC):** Built on Spatie Laravel-Permission with granular permissions (`application.view`, `mining.create`, `environment.b2.review`).
* **Branch Multi-Tenancy:** Office branches (e.g. Salem, Namakkal, Dharmapuri) with automatic query scoping via `BranchScope`.
* **Master Registries:** Centralized masters for 38 Tamil Nadu districts, minerals (Rough Stone, Granite, Gravel), lease categories, and document fields.

---

## 3. Technology Stack & Operating Environment

| Component | Technology | Specification / Package |
| :--- | :--- | :--- |
| **Backend Language** | PHP | `^8.2.12` |
| **Framework** | Laravel | `12.62.0` (Latest 12.x generation) |
| **Database** | MySQL / MariaDB | InnoDB engine, utf8mb4 collation, 64 tables |
| **Frontend Tooling** | Node.js & Vite | Vite 7.0.7, `@tailwindcss/vite`, Tailwind CSS v4 |
| **Frontend Scripting** | JavaScript / jQuery | jQuery 3.6+, Bootstrap 5.3+, DataTables, SweetAlert2 |
| **Authentication** | Laravel Session Auth | Session-based stateful authentication via `sessions` table |
| **Authorization** | Spatie Permission | `spatie/laravel-permission: ^6.25` |
| **Queue Subsystem** | Laravel Queue | `database` driver (`jobs`, `failed_jobs`, `job_batches`) |
| **Cache Subsystem** | Laravel Cache | `database` driver (`cache`, `cache_locks`) |
| **File Storage** | Local Disk | Public uploads under `public/uploads/{module}/` |
| **Automated Testing** | PHPUnit | PHPUnit 11.5.50 (Feature test suites) |

---

## 4. Key Architectural Decisions & Conventions

1. **Source Code is the Ground Truth:** Documentation must reflect the actual code implementation, not legacy proposals.
2. **Dedicated Document Tables per Module:** Rather than a monolithic polymorphic table, each domain maintains its own document table (`lease_documents`, `mining_documents`, `environment_documents`, `ppt_documents`, `dgps_documents`, `drone_documents`, `ec_compliance_documents`) to prevent MySQL table locking contention under high upload volume.
3. **Controller-Centric Business Logic:** The application uses a classic monolithic MVC architecture with Fat Controllers. Validation rules, database transactions, directory creation, and file uploads are handled directly within controller methods.
4. **Draft Persistence via Database & Sessions:** Multi-step wizards maintain state through both `session('lease_draft')` for intermediate steps and database draft records (`status = 'draft'`), enabling applicants to pause and resume intake without data loss.
5. **Universal Common ID Convention:** Cross-module tracking uses `GTMS-{YEAR}-{SEQUENCE}` (e.g., `GTMS-2026-0001`), preserved across Lease, Mining, and EC modules.
6. **Encrypted Credentials at Rest:** Sensitive state portal credentials (e.g. MIMAS passwords) are encrypted via Laravel's `Crypt::encryptString()` or model casts, and masked in HTML forms with `__UNCHANGED__` placeholders.
7. **Zero Tamil Characters in Source Code:** Application source code, variable names, database columns, and comments strictly use English, maintaining international engineering standards while business forms present English/Tamil bilingual field labels to end users.

---

## 5. System Scope & Boundary Definitions

### In Scope
* Customer directory and multi-faceted search.
* 8-step Lease Application intake and scrutiny pipeline.
* 6-stage Mining Plan workflow and document vault.
* Category B1 (2-stage ToR/EIA) and Category B2 Environmental Clearance.
* PPT Department presentation gates.
* DGPS and Drone survey data/document logging.
* EC half-yearly statutory compliance monitoring.
* Commercial Proforma and Tax Invoice generation with Indian numbering word algorithms.
* Role-based access control and multi-branch tenancy.

### Out of Scope / Deliberately Excluded
* Real-time GPS truck dispatch tracking.
* Direct integration with Tamil Nadu state treasury payment gateways (payments are manually recorded in the polymorphic ledger).
* Direct automated scraping of the state MIMAS portal (credentials are stored securely for officer manual copy-paste access).
* Public-facing unauthenticated customer self-service portals (the system is an internal office ERP used by staff, geologists, and registered consultants).

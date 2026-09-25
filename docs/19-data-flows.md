# GTMS — Statutory Data Flows & Sequence Diagrams

**Project Name:** Granite / Mining Tracking Management System (GTMS)  
**Domain:** District Mining Office Management & Statutory Regulatory ERP  
**State / Region:** Tamil Nadu, India  
**Target Architecture:** Monolithic Laravel 12 Enterprise Application  
**Authoritative Source:** Codebase Inspection & Empirical Routing/Controller Audit  
**Document Number:** `19` of `23`

---

## 1. Executive Summary

The **Granite / Mining Tracking Management System (GTMS)** orchestrates complex, multi-year statutory compliance workflows across diverse state and central regulatory departments in Tamil Nadu:
* Department of Geology and Mining (DoGM) / District Collectorate
* State Environmental Impact Assessment Authority (SEIAA) / MoEFCC Parivesh
* District Expert Appraisal Committee (DEAC) / PPT Department
* Geodetic Cadastral & Drone Aerial Photogrammetry Survey Authorities

This document details the exact **sequence diagrams and execution data flows** governing the system's six critical statutory pipelines.

---

## 2. Universal Customer Intake & AJAX Auto-Fill Sequence

Every statutory wizard in GTMS (Lease Application, Mining Portal, Environment Clearance, PPT, DGPS, Drone, EC Compliance) begins by establishing a binding connection to a master applicant profile via the **Customer Unique ID** (`mimas_no`).

```mermaid
sequenceDiagram
    autonumber
    actor User as Field Officer / Operator
    participant Browser as Client Browser (Blade DOM)
    participant Route as routes/web.php
    participant Controller as CustomerDirectoryController
    participant Model as Customer Model
    participant DB as MySQL (gtms_data)

    User->>Browser: Types Customer ID (e.g. 'TN-MMS-SLM-001') or Phone/Aadhaar
    Browser->>Browser: Debounce keystroke (300ms)
    Browser->>Route: GET /customers/lookup-mimas/{mimas_no}
    Route->>Controller: lookupByMimas(mimas_no)
    Controller->>Controller: Clean digits & strip formatting ($cleanDigits)
    Controller->>Model: Query matching mimas_no, slug, ID, or phone
    Model->>DB: SELECT * FROM customers WHERE mimas_no = ? OR mobile_num LIKE ?
    DB-->>Model: Customer Row + District + Mineral relations
    Model-->>Controller: Customer instance
    Controller-->>Browser: JSON { status: 1, message: "Found", customer: { ... } }
    Browser->>Browser: Populate DOM elements (client_name, company_name, mobile, address)
    Browser->>Browser: Apply visual cues (.field-autofilled, border-success)
    Browser->>Browser: Set hidden input name="customer_id" value="ID"
    Browser-->>User: Displays green badge "Customer Profile Auto-filled from Registry"
```

### Technical Implementation Details
* **Endpoint:** `GET /customers/lookup-mimas/{mimas_no}`
* **Source:** `app/Http/Controllers/CustomerDirectoryController.php:293-349`
* **Multi-Factor Normalization:** If the input string contains 10 or more digits, the query automatically searches `mobile_num`, `secondary_mobile_num`, and Aadhaar without spaces or hyphens:
  ```sql
  REPLACE(REPLACE(COALESCE(aadhaar_no, ''), '-', ''), ' ', '') LIKE '%cleanDigits%'
  ```
* **Client Behavior:** Fields are marked with `.field-autofilled` and locked to read-only where appropriate, preventing accidental corruption of master customer records during application creation.

---

## 3. Lease Application -> Scrutiny -> Approval -> Mining Promotion

The statutory progression from an initial quarry lease application to an approved mining plan involves formal revenue document scrutiny, collectorate approval, and an atomic cross-module data and file transfer.

```mermaid
sequenceDiagram
    autonumber
    actor Officer as District Mining Officer
    participant Browser as Web Browser
    participant CustCtrl as CustomerController
    participant LeaseModel as LeaseApplication
    participant MiningModel as MiningApplication
    participant FileSys as Local Filesystem (public/uploads)
    participant ActLog as ActivityLog Model
    participant DB as MySQL (gtms_data)

    Note over Officer,DB: Phase 1: Intake & Revenue Scrutiny
    Officer->>Browser: Complete 8-Step Wizard
    Browser->>CustCtrl: POST /application/submit
    CustCtrl->>DB: DB::transaction (Insert LeaseApplication, minerals, documents, handlers)
    DB-->>CustCtrl: Committed (Application No: LA-2026-0001, Common ID: GTMS-2026-0001)

    Officer->>Browser: Scrutinize 19 Folder Documents
    Browser->>CustCtrl: POST /application/document/{id}/status (status = 'validated')
    CustCtrl->>DB: Update lease_documents SET status = 'validated'

    Note over Officer,DB: Phase 2: Collectorate Approval
    Officer->>CustCtrl: POST /application/{id}/approve
    CustCtrl->>LeaseModel: update(['status' => 'approved'])
    CustCtrl->>ActLog: create(['action' => 'lease_approved'])

    Note over Officer,DB: Phase 3: Move to Mining Promotion (CustomerController@moveToMining)
    Officer->>Browser: Click "Move to Mining Plan" (Verified Lease status = 'approved')
    Browser->>CustCtrl: POST /application/{id}/move-to-mining
    CustCtrl->>LeaseModel: findOrFail($id) (Verify lease status = 'approved')
    CustCtrl->>MiningModel: Check Idempotency: lease->miningApplications()->first()
    alt Already Promoted (Idempotent Guard)
        CustCtrl-->>Browser: Redirect to /miningplan (/process?id={existingId}) with info message
    else First-Time Promotion
        CustCtrl->>CustCtrl: Resolve/Retain Common ID (GTMS-2026-0001)
        CustCtrl->>CustCtrl: Generate Mining App No (MP-2026-0001)
        CustCtrl->>MiningModel: MiningApplication::create([customer_id, lease_application_id, attributes...])
        CustCtrl->>DB: Sync mineral pivot (miningApp->minerals()->sync(...))
        CustCtrl->>FileSys: mkdir("public/uploads/mining/MP-2026-0001")
        loop For Each Attached Lease Document
            CustCtrl->>FileSys: @copy(lease_doc_path, mining_doc_path)
            CustCtrl->>DB: INSERT INTO mining_documents (folder_id, file_path, status='validated'/'uploaded')
        end
        CustCtrl->>DB: Initialize standard required MiningDocument placeholders (status='pending')
        CustCtrl->>ActLog: create(['action' => 'created_from_lease', 'loggable_id' => miningApp.id])
        CustCtrl-->>Browser: Redirect to /miningplan (/process?id={miningAppId}) with flash success
    end
```

### Key Architectural Constraints
* **Immutability of Common ID:** The identifier `GTMS-{YEAR}-{SEQUENCE}` is generated at Step 8 of the lease intake and is guaranteed never to mutate as the project transitions into Mining, Environment, PPT, and Survey departments.
* **Physical Document Isolation:** GTMS rejects shared file references. Files are physically cloned on disk (`@copy()`) to ensure that future document edits or revocations in the Mining module do not alter the historical audit trail of the original Lease grant.
* **Non-Transactional Cross-Module Execution:** In `CustomerController@moveToMining`, writes (`MiningApplication`, `MiningDocument`, `@copy()` file cloning, and `ActivityLog`) occur directly without an enclosing `DB::beginTransaction()` / `DB::commit()` transaction block, and handlers/payment ledgers are not duplicated from lease to mining (see `docs/22-unknowns-risks.md § RISKS-02`).

---

## 4. Mining Plan 6-Stage Process Flow (Process 6.1 – 6.6)

The formulation and approval of a Mining Plan under Rule 41 of the Tamil Nadu Minor Mineral Concession Rules, 1959 proceeds through six sequential stages:

```mermaid
stateDiagram-v2
    [*] --> Stage_6_1: New Application Intake
    
    state "Stage 6.1: Draft Mine Plan Preparation" as Stage_6_1 {
        [*] --> Draft_Preparation
        Draft_Preparation --> Baseline_Studies: Surface & Geological Plates
        Baseline_Studies --> PMCP_Design: Progressive Mine Closure Plan
        PMCP_Design --> [*]
    }

    Stage_6_1 --> Stage_6_2: Advance Stage (advanceStage)
    
    state "Stage 6.2: Departmental Scrutiny" as Stage_6_2 {
        [*] --> DoGM_Filing
        DoGM_Filing --> Scrutiny_Letter: Scrutiny Remarks Issued
        Scrutiny_Letter --> Rectification_Done: Queries Answered
        Rectification_Done --> [*]
    }

    Stage_6_2 --> Stage_6_3: Advance Stage
    
    state "Stage 6.3: Technical Committee Defense" as Stage_6_3 {
        [*] --> PPT_Formulation
        PPT_Formulation --> Committee_Defense: Presentation to Technical Committee
        Committee_Defense --> Recommendation_Secured: Minutes of Meeting
        Recommendation_Secured --> [*]
    }

    Stage_6_3 --> Stage_6_4: Advance Stage
    
    state "Stage 6.4: Joint Site Inspection" as Stage_6_4 {
        [*] --> Field_Visit
        Field_Visit --> Geo_Inspection: AD Mines & Surveyor Inspection
        Geo_Inspection --> Inspection_Report: Ground Truth Verified
        Inspection_Report --> [*]
    }

    Stage_6_4 --> Stage_6_5: Advance Stage
    
    state "Stage 6.5: Financial Assurance" as Stage_6_5 {
        [*] --> Bank_Guarantee
        Bank_Guarantee --> Final_Plates: Final Fair Copies Prepared
        Final_Plates --> [*]
    }

    Stage_6_5 --> Stage_6_6: Advance Stage
    
    state "Stage 6.6: Formal Approval Order" as Stage_6_6 {
        [*] --> Order_Issued: Approved by Department
        Order_Issued --> Move_To_Env: Click "Move to Environment Clearance"
    }

    Stage_6_6 --> [*]: Promoted to Category B1 / B2 Project
```

---

## 5. Category B1 2-Stage Lifecycle & PPT Presentation Gates

Category B1 projects require a mandatory two-stage statutory progression before the State Environmental Impact Assessment Authority (SEIAA) and District Expert Appraisal Committee (DEAC):
1. **Stage 1 (SC1):** Terms of Reference (ToR) presentation and approval.
2. **Stage 2 (SC2):** Environmental Impact Assessment (EIA) formulation, public hearing, and Final EC presentation.

```mermaid
sequenceDiagram
    autonumber
    actor Officer as Environmental Consultant
    actor DEAC as Appraisal Committee / PPT Officer
    participant EnvCtrl as EnverionsoneController
    participant PptCtrl as PptDepartmentController
    participant EnvProj as EnvironmentProject Model
    participant PptApp as PptApplication Model
    participant DB as MySQL (gtms_data)

    Note over Officer,DB: Stage 1 (SC1): ToR Preparation & Gate
    Officer->>EnvCtrl: POST /eviron (category='B1', sub_category='SC1')
    EnvCtrl->>DB: DB::transaction (Create project, b1_stage='sc1_prep', 5 default folders)
    DB-->>EnvCtrl: Project Created (ENV-B1-2026-0001)

    Officer->>EnvCtrl: Upload 5 Statutory Folders (Mining Plan, Form-1, PFR, Baseline, Draft ToR)
    Officer->>EnvCtrl: POST /eviron/{id}/submit-sc1-ppt
    EnvCtrl->>DB: DB::transaction
    EnvCtrl->>PptApp: create(presentation_stage='tor_presentation', status='agenda_scheduled')
    EnvCtrl->>EnvProj: update(b1_stage='sc1_ppt_review', status='validation', ppt_stage_1_id)
    EnvCtrl-->>Officer: Flash "SC1 submitted to PPT Department"

    Note over DEAC,DB: Gate 1: PPT Department ToR Appraisal
    DEAC->>PptCtrl: POST /ppt-department/{pptId}/approve-stage
    PptCtrl->>DB: DB::transaction
    PptCtrl->>PptApp: update(status='approved')
    PptCtrl->>EnvProj: update(sub_category='SC2', b1_stage='sc2_prep', status='draft')
    PptCtrl-->>DEAC: Redirect with success

    Note over Officer,DB: Stage 2 (SC2): EIA Preparation & Gate
    Officer->>EnvCtrl: GET /eviron/{id}
    Note right of Officer: UI automatically displays SC2 status and expands to 6 EIA Folders
    Officer->>EnvCtrl: Upload 6 Folders (EIA Report, EMP, Public Hearing, SPCB NOC, Risk Plan, Final MP)
    Officer->>EnvCtrl: POST /eviron/{id}/submit-sc2-ppt
    EnvCtrl->>DB: DB::transaction
    EnvCtrl->>PptApp: create(presentation_stage='final_ec_presentation', status='agenda_scheduled')
    EnvCtrl->>EnvProj: update(b1_stage='sc2_ppt_review', status='validation', ppt_stage_2_id)
    EnvCtrl-->>Officer: Flash "SC2 submitted to PPT Department"

    Note over DEAC,DB: Gate 2: PPT Department Final EC Appraisal
    DEAC->>PptCtrl: POST /ppt-department/{pptId}/approve-stage
    PptCtrl->>DB: DB::transaction
    PptCtrl->>PptApp: update(status='approved')
    PptCtrl->>EnvProj: update(b1_stage='completed', status='approved')
    PptCtrl-->>DEAC: Redirect with success

    Officer->>EnvCtrl: GET /eviron/{id}
    Note right of Officer: Alert: "Category B1 Lifecycle Complete!" - Displays "Issue EC Certificate" Button
```

---

## 6. EC Certificate Issuance 6-Step Stepper Wizard

Following statutory approval, official clearance orders are codified into verified certificates through the guided 6-step wizard:

```mermaid
flowchart TD
    S1["Step 1: Project Selection & Parivesh Details\n(Environment Project ID, SEIAA Reference No, Parivesh No, Validity Years)"]
    S2["Step 2: Upload Official EC PDF\n(Strict 50MB PDF upload, physical file storage, old file replacement)"]
    S3["Step 3: In-Browser Visual Preview\n(Interactive PDF viewer, condition clauses verification)"]
    S4["Step 4: Statutory Folder Destination\n(Automatic routing to 'EC Certificate & Statutory Grants' folder)"]
    S5["Step 5: Applicant Communication\n(Dispatch notification, recipient email/phone, official dispatch log)"]
    S6["Step 6: Review & Final Grant Issuance\n(Verification checklist, digital seal rendering, DB::transaction commit)"]

    S1 -->|Validate & Save| S2
    S2 -->|Validate & Save| S3
    S3 -->|Confirm Preview| S4
    S4 -->|Confirm Folder| S5
    S5 -->|Confirm Dispatch| S6
    S6 -->|POST /ec-certificate| ActiveCert["Active EC Certificate Created\n(Status = 'active', Project Status = 'approved')"]
    
    subgraph Guardrails [Adversarial & Session Guardrails]
        G1["Deep-link Protection:\nDirect URL to future step redirects to Step 1"]
        G2["Project Switch Guard:\nChanging project in Step 1 wipes downstream draft artifacts"]
        G3["Out-of-Order Lock:\nPOST to locked step redirects to Step 2 without session corruption"]
    end
```

---

## 7. Customer 360 Dossier & Commercial Invoicing Data Flow

While GTMS tracks statutory engineering compliance, each statutory entity also functions as a billable service item. The Customer 360 Dossier dynamically aggregates these cross-departmental records into unified commercial invoices.

```mermaid
sequenceDiagram
    autonumber
    actor Client as Client / Accounts Officer
    participant Browser as Web Browser
    participant TrackCtrl as CustomerTrackingController
    participant Model as Customer Aggregate Hub
    participant NumHelper as numberToWords() Helper
    participant Blade as Invoice Blade View

    Client->>Browser: Click "Tax Invoice" or "Proforma Invoice"
    Browser->>TrackCtrl: GET /customer-tracking/{customer}/tax-invoice
    TrackCtrl->>TrackCtrl: resolveCustomer(slug, id, or aadhaar)
    TrackCtrl->>Model: Eager-load 7 Child Relations
    Note over Model: loadMissing(district, mineral, leaseApplications, miningApplications, environmentProjects, ecCertificates, pptApplications, dgpsSurveys, droneSurveys, ecCompliances)

    TrackCtrl->>TrackCtrl: Iterate active applications and match SAC codes
    Note over TrackCtrl: Mining Plan (SAC 998343)<br/>Lease Application (SAC 998341)<br/>Environmental Clearance (SAC 998349)<br/>PPT Defense (SAC 998311)<br/>DGPS Land Survey (SAC 998341)<br/>Drone Aerial Survey (SAC 998342)

    TrackCtrl->>TrackCtrl: Compute Subtotal = sum(recorded services)
    TrackCtrl->>TrackCtrl: CGST = round(Subtotal * 0.09, 2)
    TrackCtrl->>TrackCtrl: SGST = round(Subtotal * 0.09, 2)
    TrackCtrl->>TrackCtrl: Grand Total = Subtotal + CGST + SGST

    TrackCtrl->>NumHelper: numberToWords(Grand Total)
    Note over NumHelper: Decomposes into Crores, Lakhs, Thousands, Hundreds, Rupees & Paise
    NumHelper-->>TrackCtrl: "Two Lakh Thirty Six Thousand Rupees Only"

    TrackCtrl->>Blade: view('pages.customer_tracking.tax_invoice', $invoiceData)
    Blade-->>Browser: Render High-Resolution Printable Statutory Invoice
    Browser-->>Client: Displays formal Tax Invoice with GSTIN, PAN, and Indian Wording
```

### Financial Calculation Formulas
* **Subtotal ($\text{ST}$):** $\sum_{i=1}^{n} \text{Amount}_i$
* **Central GST ($\text{CGST}$):** $\text{round}(\text{ST} \times 0.09, 2)$
* **State GST ($\text{SGST}$):** $\text{round}(\text{ST} \times 0.09, 2)$
* **Grand Total ($\text{GT}$):** $\text{ST} + \text{CGST} + \text{SGST}$
* **Number to Words Conversion:** Follows the traditional Indian currency denomination tiers (`Crore` $\rightarrow$ `Lakh` $\rightarrow$ `Thousand` $\rightarrow$ `Hundred` $\rightarrow$ `Rupees and Paise`).

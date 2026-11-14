# 🔄 GTMS Workflow State Machine Reference
> **Token-Saver Reference:** Consult this state machine diagram and transition table to verify valid application statuses, allowed transitions, triggering actions, and required role privileges without guessing business logic.

---

## 1. Statutory Lease Application State Machine

```mermaid
stateDiagram-v2
    [*] --> Draft : Step 1 Saved (CustomerController@saveStep1)
    Draft --> Draft : Steps 2 to 7 (MIMAS, Rule, Dossier, Payments, SF)
    Draft --> UnderScrutiny : Step 8 Final Submit (/application/submit)
    
    UnderScrutiny --> RevisionRequired : Officer flags deficiency (/application/document/{id}/status)
    RevisionRequired --> UnderScrutiny : Customer re-uploads dossier
    
    UnderScrutiny --> Approved : Scrutiny Approved (/application/{id}/approve)
    UnderScrutiny --> Rejected : Statutory Disqualification (/application/{id}/reject)
    
    Approved --> MoveToMining : Transition to Mining Portal (/application/{id}/move-to-mining)
    MoveToMining --> [*]
```

### Transition Rules Table:
| Current Status | Target Status | Trigger Endpoint | Required Permission | DB Effect |
| :--- | :--- | :--- | :--- | :--- |
| `[None]` | `draft` | `POST /step1` | `application.create` | Creates row in `lease_applications` with `status = 'draft'`. |
| `draft` | `under_scrutiny` | `POST /application/submit` | `application.create` | Locks wizard editing; assigns master project tracking code. |
| `under_scrutiny` | `revision_required`| `POST /application/document/{id}/status` | `application.edit` | Sets specific document `status = 'reupload_requested'`. |
| `under_scrutiny` | `approved` | `POST /application/{id}/approve` | `application.edit` | Marks application fully compliant and scrutiny verified. |
| `under_scrutiny` | `rejected` | `POST /application/{id}/reject` | `application.edit` | Formal statutory rejection with order copy upload. |
| `approved` | `moved_to_mining` | `POST /application/{id}/move-to-mining` | `application.edit` | Hands over dossier and provisions new row in `mining_applications`. |

---

## 2. Mining Plan Portal State Machine

```mermaid
stateDiagram-v2
    [*] --> Draft : Create LOI & Initial Plan (/newapplication)
    Draft --> Scrutiny : Submit for Technical Scrutiny
    Scrutiny --> SiteInspection : Desk Review Cleared (/mining/application/{id}/stage)
    SiteInspection --> Presentation : Quarry Boundary Ground-Truth Verified
    Presentation --> Approved : Approved by Deputy Director / Commissioner
    Approved --> MoveToEnvironment : Handover to EC Portal (/mining/application/{id}/move-to-environment)
    MoveToEnvironment --> [*]
```

### Transition Stages:
1. `draft` ➔ Initial capture of LOI date and production target.
2. `scrutiny` ➔ Geological assessment and statutory checklist verification.
3. `inspection` ➔ Revenue and Mines field officer physical boundary verification.
4. `presentation` ➔ Technical presentation before the District Mining Committee.
5. `approved` ➔ Formal 5-year Mining Plan approval order issued.

---

## 3. Environment Clearance (EC) Workflow

```mermaid
flowchart TD
    Start([Project Initialized]) --> Cat{Quarry Area Extent}
    Cat -->|< 5 Hectares| B2[Category B2: Direct Appraisal]
    Cat -->|>= 5 Hectares| B1[Category B1: Full EIA Study]
    
    B1 --> B1_SC1[B1 Subcategory 1: TOR Granted]
    B1_SC1 --> Baseline[Baseline Environmental Studies Air/Water/Noise]
    Baseline --> B1_SC2[B1 Subcategory 2: Public Hearing Proceedings]
    B1_SC2 --> FinalEIA[Final EIA Report Submission]
    
    B2 --> DirectSEIAA[Direct SEAC Committee Appraisal]
    FinalEIA --> DirectSEIAA
    
    DirectSEIAA --> ForwardPPT["Forwarded to PPT Department /eviron/{id}/submit-b2-ppt"]
    ForwardPPT --> SEACMeeting[SEAC Agenda & Committee Defense]
    SEACMeeting --> ECGranted[EC Certificate Issued /ec-certificate]
    ECGranted --> Compliance[Half-Yearly Monitoring Cycle /ec-compliance]
```

---

## 4. Technical Surveys State Machine (DGPS & Drone)

### DGPS Survey Demarcation:
* `scheduled` ➔ Survey scheduled and field officer assigned.
* `in_progress` ➔ Base station calibrated and rover boundary coordinates captured.
* `coordinates_locked` ➔ Coordinates verified against revenue boundary records.
* `completed` ➔ Certified boundary sketch and area certificate generated.

### Drone Topography Survey:
* `scheduled` ➔ DGCA flight plan submitted.
* `flight_completed` ➔ Raw aerial photos acquired.
* `processing` ➔ Orthomosaic stitching and DEM generation in GIS engine.
* `deliverables_ready` ➔ GeoTIFF, DSM, Contours, and 3D Volumetric report available.

---

## 5. Accounts & Financial State Machine

```mermaid
stateDiagram-v2
    [*] --> QuotationDraft : Create Quotation (/accounts/quotations/create)
    QuotationDraft --> QuotationSent : Client Shared
    QuotationSent --> QuotationApproved : Terms & Concessions Accepted
    
    QuotationApproved --> PaymentPending : Billed / Invoiced
    PaymentPending --> PartiallyPaid : Partial Remittance Recorded (/accounts/payments)
    PartiallyPaid --> FullyPaid : Balance Zero
    PaymentPending --> FullyPaid : Full Single Payment
    
    FullyPaid --> ReceiptIssued : Official Money Receipt Generated (/accounts/receipts)
    ReceiptIssued --> [*]
```

---

## 6. Universal Master Pipeline Bidirectional Transitions

GTMS implements a resilient bidirectional state machine across all 7 statutory departments via `MasterPipelineController`:

| Action | HTTP Endpoint | Direction | Conditions & Rules |
| :--- | :--- | :--- | :--- |
| **Forward** | `POST /master-pipeline/forward/{id}` | Downstream (e.g. `Lease` ➔ `Mining` ➔ `Environment`) | Prior department must be `approved` or scrutiny cleared. |
| **Return** | `POST /master-pipeline/return/{id}` | Upstream (e.g. `PPT` ➔ `Mining`) | **Mandatory Scrutiny Remarks:** Requires non-empty explanation of reasons for defect or correction request. |
| **Resubmit** | `POST /master-pipeline/resubmit/{id}` | Downstream | Re-advances application after corrected files/dossiers are uploaded. |

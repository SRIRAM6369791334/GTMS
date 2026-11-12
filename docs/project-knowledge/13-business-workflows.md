# 13 — Business Workflows

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Rules: [12-business-logic.md](12-business-logic.md). Diagrams reflect code paths that were read (VERIFIED) unless tagged otherwise.

## W0. End-to-end statutory journey (as the code links modules)

```mermaid
flowchart LR
  C[Customer directory] --> L[Lease application]
  L -->|approved: move-to-mining| M[Mining plan]
  L -.optional link.-> D1[DGPS survey]
  L -.optional link.-> D2[Drone survey]
  M -->|move-to-environment, no gate| E[Environment project B1/B2]
  E -->|submit-*-ppt| P[PPT application]
  P -->|approvePresentation| E
  E -->|EC certificate store sets approved| EC[EC certificate]
  EC -.linked.-> HYC[EC compliance half-yearly]
  subgraph Accounts
    Q[Quotation] --> PAY[Payment collection] --> R[Receipt]
  end
  L & M & E & P & D1 & D2 & EC & HYC --> PAY
  MP[(Master project GTMS-YYYY-NNNN)] -.forward/return/resubmit.- L & D1 & D2 & M & E & P
```

## W1. Lease application lifecycle

```mermaid
stateDiagram-v2
  [*] --> draft: step1 saves (LA-DRAFT-YYYY-NNNN)
  draft --> draft: steps 2-8 (session + partial DB updates)
  draft --> under_scrutiny: POST /application/submit (renumber LA-YYYY-NNNN)
  under_scrutiny --> validated: validate action=pass
  under_scrutiny --> revision_required: validate fail / reject
  validated --> revision_required: reject
  revision_required --> under_scrutiny: resume draft + resubmit (INFERRED: submit path reuses application_id)
  under_scrutiny --> approved: approve (allowed without validation)
  validated --> approved: approve
  approved --> [*]: move-to-mining (creates MP-...)
```
**Failure paths:** validation errors are returned as JSON 422. A missing session draft at `submit` produces whatever partial data exists in the session (PARTIALLY VERIFIED). A duplicate `application_no` raises a DB unique violation (possible for non-admin branch users; see [21](21-known-issues-risks.md) K5).
**Completion:** `approved`, plus a linked mining application.

## W2. Mining plan

```mermaid
stateDiagram-v2
  [*] --> draft_autosave: autosave (current_step 1-9)
  [*] --> scrutiny: store (stage 6.2)
  state "lease move-to-mining" as fromLease
  fromLease --> draft: stage 6.1
  draft --> scrutiny: advanceStage 6.2
  scrutiny --> approved: advanceStage 6.3/6.4
  approved --> archived: advanceStage 6.5
  scrutiny --> environment: move-to-environment (any status)
```
Documents: upload → `uploaded`; validate → `validated` / `revision_required`.

## W3. Environmental clearance — B1 (two presentations)

```mermaid
sequenceDiagram
  participant Env as Environment project (B1)
  participant PPT as PPT application
  Env->>Env: store (b1_stage=sc1_prep, sub_category=TOR, status=draft)
  Env->>PPT: submit-sc1-ppt -> create PPT tor_presentation, status agenda_scheduled, fee 25000 paid
  Env->>Env: b1_stage=sc1_ppt_review, status=validation
  PPT->>PPT: approvePresentation -> status approved
  PPT->>Env: sub_category=ETA, b1_stage=sc2_prep, status=draft
  Env->>PPT: submit-sc2-ppt (requires ETA) -> create PPT final_ec_presentation
  Env->>Env: b1_stage=sc2_ppt_review, status=validation
  PPT->>PPT: approvePresentation
  PPT->>Env: b1_stage=completed, status=approved
  Note over Env: EC certificate store also sets status=approved
```

## W4. Environmental clearance — B2
`store (B2, draft)` → `submit-b2-ppt` → PPT `b2_presentation` (`agenda_scheduled`), environment status `validation` → PPT approval → environment `approved`, `b1_stage = completed` → EC certificate.

## W5. EC certificate
Wizard steps (session `ec_wizard`, gated by `getMaxUnlockedStep`) → `store`: `updateOrCreate` by `ec_ref_no`, status `active`, handlers + `application_payments`, environment project → `approved`, activity log. Later `updateStatus` accepts any string (designed: active / expired / surrendered / revoked; no expiry automation).

## W6. EC compliance (half-yearly)
8-step wizard → `store` (`HYC-YYYY-NNNN`, status `uploaded_to_parivesh`, documents in 4 folder categories) → `updateStatus` (free text; designed path draft → documents_collected → lab_analysed → report_prepared → uploaded_to_parivesh → completed → archived). Note: store **skips** to `uploaded_to_parivesh`.

## W7. DGPS / Drone surveys
6-step wizard → `store` (DGPS: `completed` + `verified` immediately; Drone: default `deliverables_ready`) → document upload / remove. No transition endpoints exist for surveys apart from the master pipeline's `updateDepartmentRecordStatus`.

## W8. Accounts

```mermaid
flowchart TD
  Q1[Create quotation GTMS/QTN] --> Q2[status draft/sent/accepted/... set manually]
  Q2 --> Print[print view + seal image]
  D[Payment desk: select customer] --> Dues[AJAX customer-dues across all modules]
  Dues --> S[POST /accounts/payments]
  S -->|lock app row| U[update paid/pending/status + application_payments]
  U --> RC[payment_receipts GTMS/REC]
  RC --> RP[receipt show/print]
  RC --> LG[customer ledger + reports + CSV]
  T[Customer tracking] --> INV[Proforma / Tax invoice HTML - not stored]
```

## W9. Master pipeline (parallel mechanism)

```mermaid
stateDiagram-v2
  [*] --> lease_in_progress: create-from-lease (GTMS-YYYY-NNNN, movement Initiation)
  lease_in_progress --> next_in_progress: forward (rank >= current)
  next_in_progress --> returned_for_correction: return (reason, priority) + notifications
  returned_for_correction --> in_progress: resubmit (rectification notes)
```
**Interaction with the module buttons:** `moveToMining`, `moveToEnvironment` and `submit*ToPpt` do **not** create pipeline movements or update `master_projects.current_stage`. The pipeline's `forward` does not create department records; it only links IDs passed in the payload. The two mechanisms can therefore disagree about where a project is (VERIFIED by reading both code paths; no shared call).

## W10. Large file upload
init → chunks (retry, resume) → assemble (stream, SHA-256) → job chain (MIME detection, checksum verification, video thumbnail) → the module endpoint links the file to a document row through `upload_token`.

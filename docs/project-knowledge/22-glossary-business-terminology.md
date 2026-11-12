# 22 — Glossary & Business Terminology

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Domain terms are given as used in code, comments and seeded data. Legal definitions are summarised only as far as the code reflects them. Confirm legal meanings with the client.

| Term | Meaning in GTMS | Where in code |
|---|---|---|
| GTMS | The consultancy's system; prefix of the master / common ID (`GTMS-YYYY-NNNN`) and of quotation / receipt numbers | everywhere |
| Customer | Quarry or mine operator (individual or firm) the consultancy works for; not a system user | `customers` |
| Branch / Department | Organisational unit used for data isolation; the UI calls it "Department / Branch" | `branches`, `BranchScope` |
| Lease application (LA) | Application for a quarry lease of minor minerals | `lease_applications`, `CustomerController` |
| MIMAS | Tamil Nadu state Mineral Management System portal; customers have a MIMAS number and per-lease MIMAS login credentials | `customers.mimas_no`, `mimas_credentials` |
| Rule 12 / Rule 19(1) / MDCC | Lease categories ("Category Under Rule") from the TN Minor Mineral Concession Rules, 1959; custom rules allowed | `lease_categories` |
| Patta / Poramboke | Land types: private titled land / government land | `lease_categories.land_type` |
| Adangal, A-register, Patta & EC (Encumbrance Certificate), FMB | Land-record documents in the lease document checklist | `CustomerController@submit` doc list |
| Taluk / Village / Survey number | Revenue geography and parcel identifiers | lease, mining, master tables |
| Area extent (H.A.SqM) | Area written as hectares.ares.sq-m (e.g. `1.90.57`); converted to decimal hectares | `parseAreaExtentToFloat` |
| Mining plan (MP) | Plan for working the quarry, prepared by an RQP, with stages 6.1–6.6 | `mining_applications` |
| RQP | Recognised Qualified Person (prepares the mining plan) | `rqp_name`, `rqp_reg_no` |
| Nature of work | Type of mining-plan engagement (e.g. Fresh, Mining Plan, Others); drives the required documents | `nature_of_works` |
| Plan type | Mining Plan, Revised, Modified, Scheme | `plan_types` |
| PMCP | Progressive Mine Closure Plan (appears in invoice text) | `CustomerTrackingController` |
| Stage 6.1 … 6.5 | Process-flow step codes: 6.1 draft, 6.2 scrutiny, 6.3 / 6.4 approved, 6.5 archived (code map); also used as lease "Process Flow 6.2-6.4" in route comments | `MiningController@advanceStage`, routes |
| EC | Environmental Clearance | environment module |
| Category B1 / B2 | EC project categories. B1 needs ToR and a two-stage appraisal; B2 is a single appraisal | `environment_projects.category` |
| ToR / TOR | Terms of Reference, the first B1 stage (formerly "SC1") | `sub_category` |
| ETA (formerly SC2) | Second B1 stage: EIA study and TNPCB submission (code label "EIA Study & TNPCB Submission") | `sub_category`, `b1_stage` |
| EIA | Environmental Impact Assessment | labels |
| TNPCB | Tamil Nadu Pollution Control Board | labels |
| PARIVESH | National online portal for environmental clearances; GTMS stores app / ack numbers and login credentials | several tables |
| SEAC / SEIAA | State Expert Appraisal Committee / State Environment Impact Assessment Authority; presentations happen before them | PPT module |
| PPT | "Presentation" department: preparing and attending SEAC / SEIAA presentations (`tor_presentation`, `final_ec_presentation`, `b2_presentation`) | `ppt_applications` |
| EDS / ADS | Essential / Additional Details Sought (committee query outcomes) | `ppt_agendas.outcome` (unused) |
| EC certificate | Issued clearance record (Grant / Rejection / ToR communication) with validity and expiry | `ec_certificates` |
| Half-yearly compliance (HYC) | Six-monthly EC compliance return with NABL lab monitoring, uploaded to PARIVESH | `ec_compliances` |
| NABL | National Accreditation Board for Testing and Calibration Laboratories | `nabl_*` |
| DGPS survey | Differential GPS boundary / pillar demarcation survey | `dgps_surveys` |
| Drone survey | UAV topography / volumetric survey (orthomosaic, point cloud); pilot RPC no., drone UIN | `drone_surveys` |
| RPC / UIN | Remote Pilot Certificate / Unique Identification Number of a drone | `drone_surveys` |
| GSD | Ground Sampling Distance (cm per pixel) | `gsd_cm_px` |
| CBM | Cubic metres (default mineral unit) | `minerals.default_unit` |
| Seigniorage | Royalty fee per dispatch (unused stock module) | `mineral_dispatches` |
| Handler | A named person and free-text role attached to an application (e.g. who is handling it) | `application_handlers` |
| Product value / paid / pending | Fee agreed for a service, amount collected, balance | money columns |
| Quotation (QTN) | Fee proposal with scope sections, milestones, items (SAC codes), 18% GST default | `quotations` |
| SAC | Services Accounting Code (GST), e.g. 998343 | `quotation_items.sac_code`, invoices |
| Receipt (REC) | Money receipt for a payment against an application or "general" | `payment_receipts` |
| Proforma / Tax invoice | Printable invoice generated on the fly from the customer dossier; not stored | `CustomerTrackingController` |
| Seal | Uploaded seal / signature image printed on invoices and receipts | `InvoiceSealController` |
| Master project | Universal record linking one customer project across departments | `master_projects` |
| Movement | Audit entry of forward / return / resubmit between departments | `pipeline_movements` |
| Returned for correction | Pipeline status after a downstream department sends a file back, with a reason, priority and mistake category | `MasterPipelineService` |
| Common ID | String link (`GTMS-YYYY-NNNN`) copied onto department records | `common_id` |
| Dossier | Full view of a lease application (`viewapplication`) or a customer 360° view (tracking) | views |
| Chunked upload | Resumable large-file upload in 10 MB chunks | `ChunkedUploadService` |

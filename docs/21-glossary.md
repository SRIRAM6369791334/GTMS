# GTMS — Comprehensive Statutory Mining, Environmental & Domain Glossary

**Project Name:** Granite / Mining Tracking Management System (GTMS)  
**Domain:** Tamil Nadu Mining, Revenue Land Administration, and Environmental Regulatory Compliance  
**Authoritative Source:** Codebase Inspection, Regulatory Statutes, and Empirical System Mapping  
**Document Number:** `21` of `23`  
**Status:** Approved Architectural Reference  

---

## 1. Overview & Regulatory Taxonomy in Tamil Nadu

The **Granite / Mining Tracking Management System (GTMS)** operates at the intersection of state mineral concession governance, cadastral revenue land administration, geodetic field engineering, and federal environmental protection laws. 

Quarrying and mining operations in Tamil Nadu are regulated through overlapping legislative frameworks and executive authorities:
* **Mines and Minerals (Development and Regulation) Act, 1957 (MMDR Act):** The federal umbrella legislation enacted by Parliament governing the development and regulation of mines and minerals in India.
* **Tamil Nadu Minor Mineral Concession Rules, 1959 (TNMMCR):** State regulations issued by the Government of Tamil Nadu under Section 15 of the MMDR Act governing granite, rough stone, gravel, earth, quartz, and feldspar.
* **EIA Notification, 2006 (as amended):** Statutory notification under the Environment (Protection) Act, 1986 issued by the Ministry of Environment, Forest and Climate Change (MoEFCC), mandating prior Environmental Clearance (EC) for all mining leases.
* **Tamil Nadu District Collectorate & Revenue Administration:** District-level administrative control overseeing land tenure verification, field measurement, and public law and order.

To ensure consistency across models, controllers, database records, and regulatory filings, this document serves as the single source of truth for statutory domain terminology.

---

## 2. Core System Identifiers & Cross-Module Metadata

### MIMAS (Mines Information and Management Automation System)
* **Statutory Meaning:** The official web portal operated by the Department of Geology and Mining (DoGM), Government of Tamil Nadu (`https://mimas.tn.gov.in/`), for online mineral administration, permit requests, transit passes, and quarry lease tracking.
* **Codebase Implementation:** 
  * `app/Models/MimasCredential.php`: Stores encrypted portal authentication details (`username`, `password` with `'password' => 'encrypted'` AES-256 cast, `district_id`, `security_questions`).
  * `CustomerController.php`: Manages credential creation, masking with `__UNCHANGED__` tokens, and state validation in Step 6.4.

### Customer Unique ID (`mimas_no`)
* **Domain Meaning:** The internal master client identification code assigned to a quarry operator or consultancy client (e.g., `TN-MMS-SLM-001`, `TN-MMS-KRR-0042`).
* **Codebase Implementation:**
  * Persisted in `customers.mimas_no` (`VARCHAR(100)`, indexed).
  * Serves as the primary autofill key across all statutory intake wizards (Lease, Mining Plan, EC, PPT, DGPS, Drone, EC Compliance).
  * Evaluated in `CustomerDirectoryController@lookupByMimas` to instantly populate client profiles across the system.

### MIMAS Portal Registration Number (`mimas_number`)
* **Domain Meaning:** The external government portal registration or acknowledgment number issued by the state DoGM system (e.g., `TN-MMS-2026/0482`).
* **Codebase Implementation:**
  * Persisted in `customers.mimas_number` alongside `customers.mimas_status`.
  * Distinct from `mimas_no` (see `docs/22-unknowns-risks.md` for dual-column audit).
  * Displayed in customer directory badges and mining project folders.

### Universal Common ID (`GTMS-{YEAR}-{SEQUENCE}`)
* **Domain Meaning:** An immutable, cross-module traceability identifier assigned to a quarry concession lifecycle upon initial intake (e.g., `GTMS-2026-0001`, `GTMS-2026-0042`).
* **Codebase Implementation:**
  * Persisted as `common_id` across `lease_applications`, `mining_applications`, `environment_projects`, `ppt_applications`, `dgps_surveys`, `drone_surveys`, and `ec_compliances`.
  * Preserved during cross-module promotion (e.g., Lease to Mining transition in `CustomerController@moveToMining`).
  * Indexed globally to allow instant search across all departmental records in `CustomerTrackingController`.

### Application Resumption Token (`?resume={id}`)
* **Domain Meaning:** A contextual URL query parameter and session mechanism allowing operators to pause complex multi-step statutory filings and resume them seamlessly from any workstation without data loss.
* **Codebase Implementation:**
  * Supported across `CustomerController` (`/application?resume={id}`), `MiningController` (`/newapplication?resume={id}`), and `EnverionsoneController`.
  * Loads draft state from database records (`status = 'draft'`), auto-fills all carried parameters (client, survey numbers, documents), and prevents duplicate records upon submission.

### Module Application Number Prefixes
| Prefix | Module | Generator Pattern | Database Column | Example |
| :--- | :--- | :--- | :--- | :--- |
| `LA-` | Lease Application | `LA-{YEAR}-{0000}` | `lease_applications.application_no` | `LA-2026-0001` |
| `MP-` | Mining Plan | `MP-{YEAR}-{0000}` | `mining_applications.application_no` | `MP-2026-0001` |
| `ENV/B1/` or `ENV/B2/` | Environment Clearance | `ENV/{CAT}/{YEAR}/{0000}` | `environment_projects.project_code` | `ENV/B1/2026/0001` |
| `GTMS/EC/` | EC Certificate | `GTMS/EC/{YEAR}/{0000}` | `ec_certificates.certificate_no` | `GTMS/EC/2026/0001` |
| `PPT-` | PPT Department | `PPT-{YEAR}-{0000}` | `ppt_applications.application_no` | `PPT-2026-0001` |
| `DGPS-` | DGPS Survey | `DGPS-{YEAR}-{0000}` | `dgps_surveys.survey_no` | `DGPS-2026-0001` |
| `DRN-` | Drone Survey | `DRN-{YEAR}-{0000}` | `drone_surveys.survey_no` | `DRN-2026-0001` |
| `HYC-` | EC Half-Yearly Compliance | `HYC-{YEAR}-{0000}` | `ec_compliances.compliance_no` | `HYC-2026-0001` |

---

## 3. Statutory Mining Acts, Rules & Government Authorities

### MMDR Act, 1957
The **Mines and Minerals (Development and Regulation) Act, 1957**. Federal statute establishing the legal regime for all mining leases in the Republic of India. Differentiates between "Major Minerals" (bauxite, iron ore, limestone; regulated directly by Central Government) and "Minor Minerals" (granite, rough stone, gravel; delegated to State Governments under Section 15).

### TNMMCR, 1959 (TNMIR)
The **Tamil Nadu Minor Mineral Concession Rules, 1959** (often referred to in administrative practice as TNMIR). The statutory rules governing quarry leases in Tamil Nadu. The system seeds and enforces specific concession rules under `lease_categories`:
* **Rule 12(2-A)(a):** Renewal of quarry leases in Poramboke (Government) lands.
* **Rule 19(1):** Grant of quarry leases in Patta (private freehold) lands to the landowner or with landowner consent.
* **Rule 19(2)(a):** Grant of quarry leases in Government / Poramboke lands via competitive tender/auction.
* **Rule 19-A:** Special concessions and conditions for granite and ornamental stone quarrying.
* **Rule 36-F:** Conditions governing the issuance of transit permits and mineral dispatch slips.
* **Rule 41 & 42:** Mandatory requirement for submission and approval of a Mining Plan and Progressive Mine Closure Plan prepared by a Recognized Qualified Person (RQP).
* **Rule 44:** Quarrying of minor minerals by government departments or local bodies for public purposes.
* **Rule 7:** Small quarry permits and general operating conditions.

### Department of Geology and Mining (DoGM)
The state nodal department headed by the Commissioner of Geology and Mining, Chennai, and represented at the district level by the Deputy Director (DD) / Assistant Director (AD) of Geology and Mining. Responsible for:
* Scrutinizing lease applications and issuing Precise Area Communication Letters.
* Technical scrutiny and formal approval of Mining Plans.
* Issuing dispatch permits, collecting seigniorage fees, and preventing illicit quarrying.

### District Collectorate / Collector Mining Section
The chief district executive and revenue authority. In Tamil Nadu, the District Collector issues the official proceedings for quarry lease grants, signs lease deed agreements, executes public auction notices for government lands, and chairs district-level environmental and monitoring committees.

### Precise Area Communication Letter (PACL)
* **Domain Meaning:** The statutory communication issued by the District Collector to a lease applicant stating that the revenue authority is satisfied with the title, inspection, and demarcation, and calling upon the applicant to submit an approved Mining Plan and Environmental Clearance within a stipulated timeframe (typically 90 to 180 days).
* **Codebase Implementation:** Folder 2 in `mining_documents` mandates the upload of the Precise Area Communication Letter as an essential statutory prerequisite.

### Mining Dues Clearance Certificate (MDCC)
* **Domain Meaning:** A statutory clearance issued by the Assistant Director of Geology and Mining certifying that the applicant has cleared all prior royalties, seigniorage fees, dead rent, surface rent, and environmental penalties across all previous quarry holdings in the state.
* **Codebase Implementation:** Seeded in `GtmsMasterDataSeeder` under `lease_categories` and validated in `document_fields`.

### Seigniorage Fee / Royalty
* **Domain Meaning:** The statutory revenue levy paid per unit volume (Cubic Meter / CBM) or weight (Metric Tonne) to the State Government for mineral extracted from a quarry. 
* **Distinction:** "Seigniorage Fee" is the statutory term used under TNMMCR for minor minerals; "Royalty" applies to major minerals.
* **Dead Rent:** The minimum annual rent payable per hectare when a quarry is inactive or when the seigniorage fee generated is lower than the statutory minimum.

---

## 4. Mining Plan Lifecycle & Technical Engineering Terminology

### Mining Plan
* **Statutory Definition:** A comprehensive technical, geological, and engineering document prepared under Rule 41 of TNMMCR, 1959. It establishes the quantum of reserves, 5-year production schedule, method of quarrying, bench geometry, machinery deployment, environmental safeguards, and mine closure measures.
* **Plan Types Seeded in GTMS:**
  1. *Mining Plan:* Fresh 5-year operational plan for a newly granted quarry.
  2. *Revised Mining Plan:* Re-estimation of production caps or boundary adjustments during an active lease term.
  3. *Modified Mining Plan:* Changes in method of extraction, safety distances, or target horizons.
  4. *Scheme of Mining:* 5-year renewal document submitted prior to the expiration of a previous 5-year plan.

### Recognized Qualified Person (RQP)
* **Statutory Meaning:** A certified mining engineer or postgraduate geologist holding accreditation from the Indian Bureau of Mines (IBM) or the Department of Geology and Mining authorized to prepare and certify statutory Mining Plans, Schemes of Mining, and Mine Closure Plans.
* **Codebase Implementation:** Captured in `mining_applications.rqp_name`, `mining_applications.rqp_reg_no`, and `ppt_applications.rqp_attending` (e.g., `Er. M. Senthil Kumar (RQP #0812)`).

### Process Flow 6.1 – 6.6 (The 6 Sequential Stages)
The lifecycle of a Mining Plan in GTMS traverses six strictly sequential approval gates (`mining_applications.stage`):
1. **Stage 6.1 — Application Submission / Draft Preparation:** Intake of applicant metadata, cadastral SF numbers, mineral selection, RQP assignment, and field data logs.
2. **Stage 6.2 — Field Inspection & Document Validation:** Physical ground verification by department surveyors and officers; automated and granular per-document validation (`MiningDocument::status = 'validated'`).
3. **Stage 6.3 — Technical Scrutiny & Plan Review:** Detailed engineering evaluation of bench parameters, reserve calculations, stripping ratios, and environmental buffers.
4. **Stage 6.4 — Client Review & Portal Sign-Off:** Presentation of finalized plan drafts to the client; recording client consent and portal credentials.
5. **Stage 6.5 — Payment Settlement & Financial Clearance:** Invoicing and ledger settlement of consultancy, government scrutiny fees, and statutory deposits.
6. **Stage 6.6 — Final Approval & Order Dispatch:** Formal issuance of the Mining Plan Approval Letter and proceeding number by the Assistant Director / Deputy Director of Geology and Mining.

### Statutory Nature of Work
Seeded in `nature_of_works` (`MiningNatureOfWorkSeeder`), determining the dynamic folder checklist required for a project:
1. **Mining Plan:** Standard 5-year extraction and reserve blueprint.
2. **Stockyard:** Dedicated mineral storage, processing, and transit yard compliance.
3. **Mine Closure Plan:** Progressive (PMCP) and Final (FMCP) mine closure documentation.
4. **Scope Work:** Preliminary feasibility, geological reserve assessment, and prospecting.
5. **Opening Notice:** Statutory notice of intention to commence quarrying operations under Rule 38.
6. **E-Tender:** Comprehensive bid docket, tender gazettes, and affidavits for public auction quarries.
7. **Short Term:** Temporary short-term quarry permits (e.g., 3-month public works extraction).
8. **Others:** Specialized mineral administrative representations.

### Engineering Parameters
* **Bench Configuration:** The stepped vertical intervals cut into rock during extraction to prevent collapse. Defined by Bench Height (typically 5.0m to 6.0m), Bench Width (minimum equal to or greater than height), and Bench Slope (typically 45° to 60°).
* **Ultimate Pit Limit (UPL):** The spatial and depth boundary beyond which mining cannot economically or safely proceed.
* **Stripping Ratio:** The ratio of overburden / waste rock (CBM) to recoverable mineral (CBM).
* **Green Belt Safety Buffer:** The statutory safety perimeter surrounding the quarry concession. TNMMCR mandates:
  * *7.5 meters:* Minimum internal buffer from adjacent patta boundaries where zero blasting or excavation is permitted, reserved exclusively for greenbelt afforestation.
  * *50 meters:* Minimum safety buffer from public roads, railways, irrigation tanks, dams, canals, and inhabited hamlets.
  * *300 meters:* Safety buffer for heavy machinery blasting operations from residential settlements.

---

## 5. Environmental Clearance (EC) Regulatory Regime

### MoEFCC (Ministry of Environment, Forest and Climate Change)
The central ministry of the Government of India responsible for formulating and enforcing national environmental legislation, national wildlife policy, and issuing Environmental Clearance regulations.

### EIA Notification, 2006
The statutory decree promulgated under the Environment (Protection) Act, 1986. Mandates that no new quarry or expansion of an existing quarry may operate without prior Environmental Clearance (EC).

### Category B1 vs. Category B2 Quarries
Under the EIA Notification and National Green Tribunal (NGT) rulings:
* **Category B1 (Large / Cluster Quarries):**
  * Concession area **> 5.00.0 Hectares**, OR smaller quarries whose boundary falls within a **500-meter cluster radius** where the aggregate area of existing, abandoned, and proposed quarries exceeds 5.00.0 Hectares.
  * *Statutory Workflow in GTMS:* Sequential 2-Stage Process:
    1. **Stage 1 (SC1):** Application for Terms of Reference (ToR) + Baseline Environmental Data collection.
    2. **Stage 2 (SC2):** Comprehensive EIA Report formulation + Public Hearing + Final Environmental Clearance presentation.
* **Category B2 (Small / Non-Cluster Quarries):**
  * Concession area **<= 5.00.0 Hectares** AND isolated (no other quarries within 500 meters).
  * *Statutory Workflow in GTMS:* Streamlined 6-folder workflow (Form-1, Form-2, Pre-Feasibility Report, Baseline EMP) requiring appraisal without mandatory public consultation.

### SEIAA-TN (State Environment Impact Assessment Authority)
The statutory 3-member authority constituted by the Central Government for Tamil Nadu under the EIA Notification, empowered to grant or reject prior Environmental Clearance based on the recommendations of SEAC.

### SEAC-TN (State Expert Appraisal Committee)
The multi-disciplinary technical body comprising environmental scientists, hydrologists, geologists, and mining experts that scrutinizes EIA/EMP reports, reviews PowerPoint presentations, and makes recommendations to SEIAA.

### DEIAA / DEAC
**District Environment Impact Assessment Authority / District Expert Appraisal Committee.** Historical district-level bodies established in 2016 for minor mineral quarries under 5 hectares, presided over by District Collectors. Following National Green Tribunal (NGT) orders, all Category B2 minor mineral appraisals have been recentralized under SEIAA/SEAC.

### Key Statutory EC Documents & Submissions
* **Form-1 & Form-2:** Official statutory application templates capturing project coordinates, water requirement, energy consumption, waste generation, and baseline parameters.
* **Pre-Feasibility Report (PFR):** Technical and commercial viability overview accompanying Form-1.
* **ToR (Terms of Reference):** Explicit baseline environmental parameters (ambient air quality, ground water hydrology, noise levels, flora/fauna, socio-economic survey) prescribed by SEAC for conducting an EIA study.
* **EIA (Environmental Impact Assessment):** The comprehensive scientific study analyzing baseline environmental health, identifying project impacts, and assessing risk models.
* **EMP (Environmental Management Plan):** The binding operational strategy mitigating pollution: dust suppression water sprinklers, acoustic berms, siltation traps, rainwater harvesting pits, and green belt sapling plantations.
* **Public Hearing (PH) / Public Consultation:** A public proceedings conducted by the Tamil Nadu Pollution Control Board (TNPCB) and presided over by the District Collector, where local residents air objections and concerns regarding proposed Category B1 quarries.
* **PARIVESH Portal:** The single-window portal (`https://parivesh.nic.in/`) developed by MoEFCC for online tracking of environmental, forest, wildlife, and coastal regulation zone clearances.
* **EDS (Essential Details Sought):** Statutory queries raised by the SEAC/SEIAA member secretary when initial documentation is incomplete.
* **ADS (Additional Details Sought):** Specific technical queries raised by SEAC during committee appraisal requiring technical field replies or certified maps.
* **CER (Corporate Environment Responsibility):** Mandatory financial commitment (typically 1% to 2% of total project capital cost) pledged by the quarry owner for village infrastructure (solar lights, school furniture, drinking water RO plants) submitted via a notarized **CER Affidavit**.

### EC Certificate
* **Domain Meaning:** The formal statutory grant order issued by the Member Secretary, SEIAA-TN, permitting quarrying operations.
* **Key Attributes Tracked in GTMS (`ec_certificates` table):**
  * *EC Order Number:* (e.g., `SEIAA-TN/F.No.9821/EC/124/2026`).
  * *Validity Period:* Typically granted for 5 years or the lease duration (maximum 30 years).
  * *Annual Production Ceiling:* Maximum allowable annual extraction capped in CBM / Metric Tonnes.
  * *Specific & General Conditions:* Mandatory operational provisos (e.g., depth restrictions, ground water table clearance, boundary fencing).

### EC Half-Yearly Compliance Monitoring (HYC)
* **Statutory Requirement:** General Condition mandated by MoEFCC and SEIAA requiring quarry owners to submit compliance reports on **June 1st** (for the period October to March) and **December 1st** (for the period April to September) throughout the lease lifespan.
* **Codebase Implementation (`ec_compliances` table):**
  * Tracks monitoring period (`April 2026 - September 2026`).
  * Captures **NABL Accredited Laboratory** test reports for Ambient Air Quality (PM10, PM2.5, SO2, NOx), Water Quality (pH, TDS, Heavy Metals), Noise Levels (Leq dB(A)), and Soil Testing.
  * Records **PARIVESH Acknowledgment Number** (`PARIVESH-ACK-TN-2026-XXXX`).

### TNPCB (Tamil Nadu Pollution Control Board)
The state regulatory agency enforcing the Water (Prevention and Control of Pollution) Act, 1974, and Air (Prevention and Control of Pollution) Act, 1981. Issues:
* **CTE (Consent to Establish):** Required prior to civil work or pit opening.
* **CTO (Consent to Operate):** Renewable operational license granting permission to extract and crush rock.

---

## 6. Technical Appraisal & Presentation (PPT Department)

### PPT Department
* **Domain Meaning:** The specialized engineering and liaison wing responsible for synthesizing complex statutory documentation into concise, authoritative PowerPoint slide decks and defending them before the appraisal committee.
* **Presentation Gates:**
  1. *Gate 1 (ToR Presentation):* Defends baseline study radius, cluster status, air monitoring stations, and hydro-geological modeling.
  2. *Gate 2 (Final EC Presentation):* Defends EIA findings, public hearing responses, EMP budgets, CER allocations, and progressive reclamation designs.
* **Operational Flow in GTMS (`ppt_applications` table):**
  * Manages committee scheduling (`agenda_scheduled`, `presented`, `approved`, `rejection`).
  * Links RQP attendance (`rqp_attending`) and authorized company representatives (`company_rep_attending`).
  * Catalogs SEAC and SEIAA official agenda and minutes dockets across 11 statutory folders.

---

## 7. Geodetic, Cadastral & Drone Surveys

### DGPS Survey (Differential Global Positioning System)
* **Domain Meaning:** High-precision satellite positioning using dual-frequency GNSS receivers (Base station fixed over known benchmark + Rover traversing boundary corners) to establish ground coordinates with sub-centimeter accuracy.
* **Coordinate System:** Universal Transverse Mercator (UTM) projection on WGS-84 datum (Tamil Nadu falls within **UTM Zone 43N** and **UTM Zone 44N**).
* **Pillar Numbering:** Physical boundary stone pillars demarcated on site as `BP-1`, `BP-2`, `BP-3`... with latitude, longitude, and elevation.
* **Codebase Implementation (`dgps_surveys` & `dgps_points` tables):**
  * Persists point sequences, latitude, longitude, northing, easting, and elevation.
  * Generates certified KML overlays for Google Earth and Survey of India Topo-sheets.

### Drone Survey (UAV Photogrammetry)
* **Domain Meaning:** Aerial photogrammetric surveying using unmanned aerial vehicles (drones) equipped with calibrated RTK/PPK cameras flying programmed grid patterns over the quarry.
* **Outputs Generated & Stored in GTMS (`drone_documents` table):**
  * *Flight Logs:* Mission telemetry, flight dates, altitude, overlap percentages.
  * *High-Resolution Orthomosaic:* Geo-referenced aerial raster image with Ground Sampling Distance (GSD) < 3 cm/pixel.
  * *Digital Elevation Model (DEM / DTM):* 3D terrain surface data capturing quarry pit depth, bench profiles, and waste dumps.
  * *Contour DXF:* AutoCAD vector contours (typically 1.0m intervals).
  * *Volumetric Cut & Fill Computation:* Precise excavation and reserve calculation comparing pre-mining and post-mining surfaces.
* **DGCA Compliance:** Adherence to Directorate General of Civil Aviation rules: certified drone pilots, Digital Sky portal flight permissions, and Nano/Micro/Small category compliances.

---

## 8. Tamil Nadu Revenue Land Records & Cadastral Terminology

Understanding Tamil Nadu revenue terminology is critical for validating land ownership, mineral rights, and statutory lease intake:

### Patta
* **Statutory Meaning:** The legal title document issued by the Revenue Department (Tahsildar) establishing that an individual or corporate entity is the registered owner (Pattadar) of a specific parcel of land.
* **Patta Number:** The unique ledger number in the village land register under which all land parcels owned by that individual are grouped.

### Chitta
The extract from the Village Land Register maintained by the Village Administrative Officer (VAO) showing details of land holdings, survey numbers, sub-divisions, land category (wetland/dryland), and assessment tax payable by the Pattadar.

### Adangal (Village Record No. 7 / Pahang)
* **Statutory Meaning:** The crucial annual village register maintained by the VAO documenting the actual physical possession, crop cultivation, tree counts, surface features, or mineral extraction happening on each survey parcel season by season (Fasli year).
* **Significance in Mining:** An Adangal stating "Rocky Outcrop" (*Parai*) or "Quarry Waste" is essential evidence confirming the mineral nature and non-agricultural character of the land.

### FMB (Field Measurement Book)
* **Statutory Meaning:** The official cadastral map of an individual survey field maintained in the Taluk Survey Office.
* **Contents:** Exact boundary lengths (in links or meters), sub-division boundary lines, traverse baseline points, and offset measurements recorded using the ladder/triangulation system.
* **Codebase Implementation:** Folder 1 in Lease and Mining applications mandates verified FMB sketches to validate boundary geometry.

### A-Register (Permanent Settlement Register)
The foundational permanent revenue register of a village compiled during the Settlement Survey. It records the survey field number, sub-division number, old survey number, government/inams classification, Ryotwari or Poramboke tenure, soil type, irrigation source, land rate, and original owner.

### S.F. No. (Survey Field Number) / Sub-division Number
* **Domain Meaning:** The unique numeric identifier assigned to a bounded parcel of land within a revenue village (e.g., `S.F.No. 102/1A`, `102/1B`).
* **Codebase Implementation:** Captured in `lease_survey_numbers` and `mining_applications.survey_numbers_text`.

### Revenue Land Classifications
* **Ryotwari Patta Land:** Private freehold land where the occupant pays land revenue directly to the government. The mineral rights for minor minerals generally belong to the state, but the landowner has the preferential right to apply for a quarry lease under Rule 19(1).
* **Poramboke Land:** Land owned by the State Government set apart for communal, public, or unassessed purposes.
  * *Giri Poramboke:* Hill and rock wasteland (prime quarry land leased under Rule 19(2)(a)).
  * *Meichal Poramboke:* Grazing ground (generally protected).
  * *Vandi Pathai / Odai Poramboke:* Cart track or stream buffer (strict 50m setback required).
* **Nanjai:** Wetland irrigated by river, canal, or tank (quarrying strictly discouraged).
* **Punjai:** Dryland dependent on rainfall or wells (standard terrain for private quarry grants).

### Area & Land Extent Units
| Unit Name | Symbol / Term | Equivalent in Metric (Hectares) | Equivalent in British (Acres / Cents) |
| :--- | :--- | :--- | :--- |
| **Hectare** | `Ha` / `Hect` | **1.00.0 Ha** ($10,000\text{ m}^2$) | 2.471 Acres / 247.1 Cents |
| **Acre** | `Ac` | 0.4047 Ha ($4,046.86\text{ m}^2$) | **1.00 Acre** / 100 Cents |
| **Cent** | `Cent` | 0.004047 Ha ($40.47\text{ m}^2$) | 0.01 Acre / **1.0 Cent** |
| **Are** | `A` | 0.01.0 Ha ($100\text{ m}^2$) | 2.47 Cents |

*Note on Tamil Nadu Revenue Format:* Extent is universally recorded in Hectares and Ares using decimal triplets (e.g., `3.85.0 Ha` represents 3 Hectares, 85 Ares, 0 Centiares = 3.85 Hectares = ~9.51 Acres).

---

## 9. Commercial Billing, Ledgers & Invoicing Terminology

### Customer 360 Dossier
The consolidated operational profile (`/customer-tracking/{customer}`) that unifies all statutory applications across 7 modules (Lease, Mining, EC, PPT, DGPS, Drone, EC Compliance) for a single client into an interactive, real-time command center.

### Application Handlers
* **Domain Meaning:** Dynamic multi-person internal staff assignments responsible for client liaison, field coordination, and physical file follow-up at government offices.
* **Codebase Implementation:** Polymorphic table `application_handlers` (`application_type`, `application_id`) binding users with designated roles (`liaison`, `technical_scrutiny`, `field_surveyor`).

### Polymorphic Financial Ledgers
* **Domain Meaning:** Every statutory project carries standardized commercial billing fields (`product_value`, `paid_amount`, `pending_amount`, `payment_status`).
* **Invoicing Engine (`CustomerTrackingController`):**
  * Aggregates active services into official **Proforma Invoices** (`GTMS/PI/{YEAR}/{0000}`) and official **Tax Invoices** (`GTMS/TI/{YEAR}/{0000}`).
  * Applies **Services Accounting Code (SAC)** categories:
    * `SAC 998341`: Geological, geophysical, and geodetic surveying services.
    * `SAC 998342`: Remote sensing, aerial photogrammetry, and cartographic services.
    * `SAC 998343`: Mining engineering, mine planning, and closure formulation.
    * `SAC 998349`: Environmental consultancy, EIA/EMP formulation, and compliance reporting.
    * `SAC 998311`: Management and regulatory appraisal defense services.
  * Enforces statutory **GST Dual Tiering:**
    * *Intra-state (Tamil Nadu to Tamil Nadu):* CGST 9.0% + SGST 9.0% (Total 18.0%).
    * *Inter-state:* IGST 18.0%.
  * Enforces the **Indian Numbering Words System** (`Crores`, `Lakhs`, `Thousands`, `Hundreds`) via `CustomerTrackingController@numberToWords` (e.g., *"Rupees One Lakh Fifty Thousand Only"*).

---

## 10. Alphabetical Quick-Reference Index (A–Z)

* **ADS:** Additional Details Sought (SEAC technical query).
* **Adangal:** Village Record No. 7 showing crop, mineral, and physical land possession.
* **A-Register:** Permanent Village Settlement Register recording land classification and soil sort.
* **BranchScope:** Multi-tenant query scope enforcing branch data isolation for non-admin users.
* **Category B1:** Quarry concession > 5 Ha or in cluster requiring 2-stage ToR and EIA appraisal.
* **Category B2:** Quarry concession <= 5 Ha non-cluster using streamlined 6-folder clearance.
* **CER:** Corporate Environment Responsibility fund committed by quarry owner.
* **CBM:** Cubic Meter ($1\text{ m} \times 1\text{ m} \times 1\text{ m}$), standard volume unit for rough stone and gravel.
* **Chitta:** Land holding extract showing total survey numbers and acreage under a Patta.
* **Common ID:** Immutable cross-module tracker (`GTMS-{YEAR}-{SEQUENCE}`).
* **CTE / CTO:** Consent to Establish / Consent to Operate issued by TNPCB.
* **DEIAA / DEAC:** District Environment Impact Assessment Authority / District Expert Appraisal Committee.
* **DGPS:** Differential Global Positioning System (sub-centimeter boundary geodetic survey).
* **DGCA:** Directorate General of Civil Aviation (regulates drone surveys and pilots).
* **DoGM:** Department of Geology and Mining, Government of Tamil Nadu.
* **EC:** Environmental Clearance issued under EIA Notification, 2006.
* **EDS:** Essential Details Sought (SEAC administrative query).
* **EIA:** Environmental Impact Assessment (scientific baseline and impact study).
* **EMP:** Environmental Management Plan (pollution mitigation and greenbelt plan).
* **FMB:** Field Measurement Book (cadastral survey map with boundary measurements).
* **HYC:** Half-Yearly Compliance monitoring report for post-EC operations.
* **MDCC:** Mining Dues Clearance Certificate certifying zero arrears.
* **MIMAS:** Mines Information and Management Automation System (Tamil Nadu DoGM portal).
* **mimas_no:** Unique master customer code in GTMS (`Customer Unique ID`).
* **mimas_number:** External state portal registration string.
* **MMDR Act:** Mines and Minerals (Development and Regulation) Act, 1957.
* **MoEFCC:** Ministry of Environment, Forest and Climate Change, Government of India.
* **NABL:** National Accreditation Board for Testing and Calibration Laboratories.
* **PACL:** Precise Area Communication Letter issued by District Collector.
* **PARIVESH:** Central single-window environmental clearance portal.
* **Patta:** Land ownership title deed issued by Revenue Tahsildar.
* **PFR:** Pre-Feasibility Report.
* **PMCP:** Progressive Mine Closure Plan under Rule 41 of TNMMCR.
* **Poramboke:** Government-owned communal, hill, or waste land.
* **PPT Department:** Technical presentation defense wing for SEAC/DEAC hearings.
* **RQP:** Recognized Qualified Person certified to draft Mining Plans.
* **Ryotwari:** Peasant-held proprietary land tenure under Patta.
* **SAC:** Services Accounting Code for GST invoicing.
* **SEAC-TN:** State Expert Appraisal Committee, Tamil Nadu.
* **SEIAA-TN:** State Environment Impact Assessment Authority, Tamil Nadu.
* **Seigniorage:** Statutory mineral extraction fee paid to State Government.
* **S.F. No.:** Survey Field Number of cadastral parcel.
* **Stockpile:** Mineral storage inventory yard tracking CBM/Tonnes in/out.
* **TNMIR / TNMMCR:** Tamil Nadu Minor Mineral Concession Rules, 1959.
* **ToR:** Terms of Reference issued by SEAC for conducting EIA studies.
* **UTM:** Universal Transverse Mercator coordinate projection system (Zone 43N / 44N).

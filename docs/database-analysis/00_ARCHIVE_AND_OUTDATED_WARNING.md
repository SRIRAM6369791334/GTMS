# ⚠️ LEGACY ARCHIVE & OUTDATED SPECIFICATION NOTICE

**Target Folder:** `/docs/database-analysis/` (All 22 Markdown Files)  
**Creation Date of this Archive Notice:** 2026-09-24  
**Original Proposal Date:** 2026-09-04  
**Status:** **SUPERSEDED / OUTDATED / HISTORICAL REFERENCE ONLY**  
**Official Single Source of Truth:** Refer to `/docs/00-project-overview.md` through `/docs/23-developer-onboarding.md`

---

## 1. Important Warning for Developers

> [!CAUTION]
> **DO NOT USE THE FILES IN THIS FOLDER AS AN ACCURATE SPECIFICATION OF THE CURRENT GTMS SYSTEM.**
> The 22 documents in this directory represent early design proposals and brainstorming questionnaires created on **September 4, 2026**, **before** the application codebase was fully developed.
> 
> As development progressed across **Phases 5.1 through 5.29**, numerous architectural, relational, and business requirement changes were implemented in the actual PHP source code and MySQL migrations.

---

## 2. Key Discrepancy Matrix (Legacy Proposal vs Code Reality)

| Subject / Topic | Outdated Legacy Proposal (In this folder) | Actual Code Implementation (Source of Truth) | Why it Changed / Engineering Rationale |
| :--- | :--- | :--- | :--- |
| **Document Storage Architecture** | Single polymorphic table (`project_documents`) shared across all modules. | **Dedicated document tables per module:** `lease_documents`, `mining_documents`, `environment_documents`, `ppt_documents`, `dgps_documents`, `drone_documents`, `ec_compliance_documents`. | High concurrency (1,000+ simultaneous uploads) would cause severe MySQL row-lock and table-lock contention on a single shared table. |
| **Mineral Selection** | Single foreign key (`mineral_id`) allowing only 1 mineral per quarry. | **Multi-mineral pivot table:** `mining_application_minerals` and `lease_application_minerals` alongside `other_mineral_name`. | Real-world Tamil Nadu quarries extract multiple composite minerals simultaneously (e.g. Rough Stone and Gravel). |
| **Customer Identification** | Debated whether customer is an inline snapshot or separate entity; labeled as MIMAS Number. | **Dedicated Central Aggregate (`Customer`):** Indexed by `slug` and `mimas_no` (serving as Customer Unique ID e.g. `TN-MMS-SLM-001`), functioning as a 360-degree hub for all 7 statutory modules. | Clean 3NF architecture; eliminates repetitive client data entry across modules and preserves referential integrity. |
| **Category B1 Environmental Clearance** | Treated as a simple single-stage document upload. | **Strict 2-Stage Sequential Statutory Lifecycle:** Stage 1 (SC1 Preparation: 5 ToR Folders) → PPT ToR Approval Gate → Stage 2 (SC2 Preparation: 6 EIA & TNPCB Folders) → PPT Final EC Gate. | Tamil Nadu environmental regulations mandate ToR approval before EIA studies can be legally commissioned. |
| **Commercial Invoicing** | Mentioned as out-of-scope or basic static receipts. | **Automated Commercial Billing Engine:** Polymorphic ledger on applications feeding into dynamic **Proforma Invoices** and official **Tax Invoices** with Indian numbering words (Crores/Lakhs) and SGST/CGST (9%+9%) vs IGST (18%) split. | Enables district consulting offices to bill clients directly from active project dossiers without separate accounting software. |
| **Application Handlers & Payments** | Not planned in initial tables. | **Polymorphic Handlers & Payments:** Dedicated `application_handlers` and compound financial fields on all statutory application records. | Allows dynamic assignment of multiple field geologists, surveyors, and legal clerks with real-time fee tracking. |
| **EC Half-Yearly Compliance** | Completely absent from early proposals. | **Dedicated Statutory Module:** `ec_compliances` and `ec_compliance_documents` tracking 6-month statutory periods and pollution testing logs. | Mandatory ongoing compliance monitoring required by MoEFCC throughout the active mining lease. |

---

## 3. Where to Find the True Documentation

For complete, accurate, and 100% verified documentation derived directly from the application source code, please refer to the main documentation suite in `docs/`:

* [`00-project-overview.md`](../00-project-overview.md) — System Overview & Business Domain
* [`01-architecture.md`](../01-architecture.md) — Master System Architecture & Flow
* [`02-environment-setup.md`](../02-environment-setup.md) — Local Development Setup
* [`03-database.md`](../03-database.md) — 64 Tables Complete Data Dictionary
* [`04-models.md`](../04-models.md) — 47 Eloquent Models & Relationship Trees
* [`05-controllers.md`](../05-controllers.md) — Method-by-Method Controller Audit
* [`06-routes.md`](../06-routes.md) — 121 Routes Complete Catalog
* [`22-unknowns-risks.md`](../22-unknowns-risks.md) — Technical Risk Register & Dead Code
* [`23-developer-onboarding.md`](../23-developer-onboarding.md) — 7-Day Developer Immersion Guide

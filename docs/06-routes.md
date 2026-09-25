# 06 — Web Routing Architecture & Route Catalog

**Document Version:** 1.0.0  
**Target Codebase:** `c:\xampp\htdocs\GTMS\gtms`  
**Classification:** Complete Routing Specification  
**Author:** Spec Miner Survey 2 (Backend Logic Specialist)  
**Total Registered Routes:** 121  
**Source of Truth:** `routes/web.php` & `php artisan route:list`  

---

## 1. Routing Architecture Overview

The GTMS routing layer is defined entirely within `routes/web.php`. The system does not register API routes under `routes/api.php`; all asynchronous client-side operations (DataTables, SweetAlert2 modals, live search autocomplete, document uploads, and stage transitions) operate as authenticated web routes protected by session cookies and CSRF tokens.

### 1.1 Middleware Hierarchy & Guardrails
- **`web` Group:** Encapsulates cookie encryption, session state (`database` driver), CSRF protection, and view error bag sharing.
- **`guest` Guard:** Restricts unauthenticated access exclusively to the login endpoints (`/`, `/login`, `POST /login`). Authenticated users attempting to view the login screen are automatically redirected to `/dashboard`.
- **`auth` Guard:** Secures all internal ERP operations, requiring an active authenticated user session.
- **Spatie Permission Middleware:** Fine-grained authorization enforced via `permission:{name}` aliases registered in `bootstrap/app.php`:
  - `permission:customer.view`, `permission:customer.create`, `permission:customer.edit`, `permission:customer.delete`
  - `permission:application.view`, `permission:application.create`, `permission:application.edit`
  - `permission:mining.view`, `permission:mining.create`, `permission:mining.edit`
  - `permission:environment.view`, `permission:environment.b2.create`, `permission:environment.b2.upload`, `permission:environment.b2.review`
  - `permission:ppt.view`
  - `permission:dgps.view`
  - `permission:drone.view`
  - `permission:roles.view`, `permission:roles.create`, `permission:roles.edit`, `permission:roles.delete`
  - `permission:users.view`, `permission:users.create`, `permission:users.edit`, `permission:users.delete`
  - `permission:branch.view`, `permission:branch.create`, `permission:branch.edit`, `permission:branch.delete`
  - `permission:category.view`, `permission:category.create`, `permission:category.edit`, `permission:category.delete`
  - `permission:product.view`, `permission:product.create`
  - `permission:unit.view`

---

## 2. Complete 121-Route Categorized Catalog

Below is the definitive catalog of all 121 routes registered in the GTMS application, categorized by business domain.

---

### Category A: Guest & Authentication (5 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 1 | `GET\|HEAD` | `/` | `login` | `AuthController@showLogin` | `web`, `guest` | Landing page / Login screen |
| 2 | `GET\|HEAD` | `login` | *None* | `AuthController@showLogin` | `web`, `guest` | Explicit login alias |
| 3 | `POST` | `login` | `login.post` | `AuthController@login` | `web`, `guest` | Form submission & credential auth |
| 4 | `POST` | `logout` | `logout` | `AuthController@logout` | `web`, `auth` | Session flush & token invalidate |
| 5 | `GET\|HEAD` | `dashboard` | `dashboard` | `Closure (pages.index)` | `web`, `auth` | Authenticated system dashboard |

---

### Category B: Customer Directory & Customer 360 Tracking (12 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 6 | `GET\|HEAD` | `customers` | `customers.index` | `CustomerDirectoryController@index` | `web`, `auth`, `permission:customer.view` | Master customer directory & KPIs |
| 7 | `GET\|HEAD` | `customers/{slug}` | `customers.show` | `CustomerDirectoryController@show` | `web`, `auth`, `permission:customer.view` | Customer 360 profile & dossier |
| 8 | `GET\|HEAD` | `customer-tracking` | `customer-tracking.index` | `CustomerTrackingController@index` | `web`, `auth`, `permission:customer.view` | Multi-faceted filter & tracking hub |
| 9 | `GET\|HEAD` | `customer-tracking/search` | `customer-tracking.search` | `CustomerTrackingController@search` | `web`, `auth`, `permission:customer.view` | Live AJAX search autocomplete |
| 10 | `GET\|HEAD` | `customer-tracking/{customer}` | `customer-tracking.show` | `CustomerTrackingController@show` | `web`, `auth`, `permission:customer.view` | Customer 360 tracking dossier |
| 11 | `GET\|HEAD` | `customer-tracking/{customer}/proforma-invoice` | `customer-tracking.proforma-invoice` | `CustomerTrackingController@proformaInvoice` | `web`, `auth`, `permission:customer.view` | Consolidated 2-Page A4 Proforma Invoice |
| 12 | `GET\|HEAD` | `customer-tracking/{customer}/tax-invoice` | `customer-tracking.tax-invoice` | `CustomerTrackingController@taxInvoice` | `web`, `auth`, `permission:customer.view` | Consolidated 1-Page A4 Tax Invoice |
| 13 | `GET\|HEAD` | `customers/lookup-mimas/{mimas_no}` | `customers.lookup.mimas` | `CustomerDirectoryController@lookupByMimas` | `web`, `auth` | Universal master lookup for autofill |
| 14 | `POST` | `customeradd` | `customeradd` | `CustomerDirectoryController@store` | `web`, `auth`, `permission:customer.create` | Register new customer entity |
| 15 | `POST` | `customeredit` | `customeredit` | `CustomerDirectoryController@update` | `web`, `auth`, `permission:customer.edit` | Update customer record |
| 16 | `POST` | `customerdelete` | `customerdelete` | `CustomerDirectoryController@destroy` | `web`, `auth`, `permission:customer.delete` | Soft delete with dependency guard |

---

### Category C: Lease Applications & Statutory Wizard (24 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 17 | `GET\|HEAD` | `application` | `application.index` | `CustomerController@index` | `web`, `auth`, `permission:application.view` | Lease applications master register |
| 18 | `GET\|HEAD` | `viewapplication` | `viewapplication` | `CustomerController@viewApplication` | `web`, `auth`, `permission:application.view` | Detailed application dossier |
| 19 | `GET\|HEAD` | `step1` | `step1` | `CustomerController@step1` | `web`, `auth`, `permission:application.create` | Wizard Step 1: Applicant info |
| 20 | `POST` | `step1` | `step1.save` | `CustomerController@saveStep1` | `web`, `auth`, `permission:application.create` | Persist Step 1 & initialize draft |
| 21 | `GET\|HEAD` | `step2` | `step2` | `CustomerController@step2` | `web`, `auth`, `permission:application.create` | Wizard Step 2: Contact & MIMAS |
| 22 | `POST` | `step2` | `step2.save` | `CustomerController@saveStep2` | `web`, `auth`, `permission:application.create` | Persist Step 2 & MIMAS credentials |
| 23 | `GET\|HEAD` | `step3` | `step3` | `CustomerController@step3` | `web`, `auth`, `permission:application.create` | Wizard Step 3: Rule 44 category |
| 24 | `POST` | `step3` | `step3.save` | `CustomerController@saveStep3` | `web`, `auth`, `permission:application.create` | Persist Step 3 category |
| 25 | `GET\|HEAD` | `step4` | `step4` | `CustomerController@step4` | `web`, `auth`, `permission:application.create` | Wizard Step 4: Folders overview |
| 26 | `GET\|HEAD` | `step5` | `step5` | `CustomerController@step5` | `web`, `auth`, `permission:application.create` | Wizard Step 5: Document upload |
| 27 | `POST` | `step5/upload` | `step5.upload` | `CustomerController@uploadDocument` | `web`, `auth`, `permission:application.create` | Upload individual statutory file |
| 28 | `GET\|HEAD` | `step6` | `step6` | `CustomerController@step6` | `web`, `auth`, `permission:application.create` | Wizard Step 6: Handling team |
| 29 | `POST` | `step6` | `step6.save` | `CustomerController@saveStep6` | `web`, `auth`, `permission:application.create` | Persist Step 6 handling team |
| 30 | `GET\|HEAD` | `step7` | `step7` | `CustomerController@step7` | `web`, `auth`, `permission:application.create` | Wizard Step 7: Payment ledger |
| 31 | `POST` | `step7` | `step7.save` | `CustomerController@saveStep7` | `web`, `auth`, `permission:application.create` | Persist Step 7 payment |
| 32 | `GET\|HEAD` | `step8` | `step8` | `CustomerController@step8` | `web`, `auth`, `permission:application.create` | Wizard Step 8: Review & summary |
| 33 | `POST` | `application/submit` | `application.submit` | `CustomerController@submit` | `web`, `auth`, `permission:application.create` | Official atomic submission |
| 34 | `GET\|HEAD` | `application/{id}/resume` | `application.resume` | `CustomerController@resumeDraft` | `web`, `auth`, `permission:application.create` | Resume draft from DB session |
| 35 | `POST` | `application/{id}/validate` | `application.validate` | `CustomerController@validateApplication` | `web`, `auth`, `permission:application.edit` | Scrutiny validation pass/fail |
| 36 | `POST` | `application/{id}/approve` | `application.approve` | `CustomerController@approveApplication` | `web`, `auth`, `permission:application.edit` | Final lease approval |
| 37 | `POST` | `application/{id}/reject` | `application.reject` | `CustomerController@rejectApplication` | `web`, `auth`, `permission:application.edit` | Send back for document revision |
| 38 | `POST` | `application/{id}/move-to-mining` | `application.moveToMining` | `CustomerController@moveToMining` | `web`, `auth`, `permission:application.edit` | Promote to Mining Plan & clone files |
| 39 | `POST` | `application/document/{id}/status` | `application.document.status` | `CustomerController@updateDocumentStatus` | `web`, `auth`, `permission:application.edit` | Individual document verification |
| 40 | `GET\|HEAD` | `application/{id}/report` | `application.report` | `CustomerController@generateReport` | `web`, `auth`, `permission:application.view` | Generate printable summary report |

---

### Category D: Mining Plan & Process Flow 6.1–6.5 (10 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 41 | `GET\|HEAD` | `miningplan` | `miningplan.index` | `MiningController@index` | `web`, `auth`, `permission:mining.view` | Mining plans master register |
| 42 | `GET\|HEAD` | `projectfolder` | `projectfolder` | `MiningController@projectFolder` | `web`, `auth`, `permission:mining.view` | 6-folder dossier & progress view |
| 43 | `GET\|HEAD` | `document` | `document` | `MiningController@Document` | `web`, `auth`, `permission:mining.view` | Folder-wise document upload console |
| 44 | `GET\|HEAD` | `process` | `process` | `MiningController@Process` | `web`, `auth`, `permission:mining.view` | Stage scrutiny & validation workflow |
| 45 | `GET\|HEAD` | `newapplication` | `newapplication` | `MiningController@newApplication` | `web`, `auth`, `permission:mining.create` | Intake wizard / resume handler |
| 46 | `POST` | `newapplication` | `newapplication.store` | `MiningController@store` | `web`, `auth`, `permission:mining.create` | Commit mining plan & atomic numbering |
| 47 | `POST` | `mining/document/upload` | `mining.document.upload` | `MiningController@uploadDocument` | `web`, `auth`, `permission:mining.create` | Upload mining checklist document |
| 48 | `POST` | `mining/document/{id}/validate` | `mining.document.validate` | `MiningController@validateDocument` | `web`, `auth`, `permission:mining.edit` | Review document (pass / flag correction) |
| 49 | `POST` | `mining/application/{id}/stage` | `mining.application.stage` | `MiningController@advanceStage` | `web`, `auth`, `permission:mining.edit` | Advance stage (6.1 through 6.5) |
| 50 | `POST` | `mining/application/{id}/move-to-environment` | `mining.application.moveToEnvironment` | `MiningController@moveToEnvironment` | `web`, `auth`, `permission:mining.edit` | Transition to Environment Clearance |

---

### Category E: Environment Clearance B1 & B2 Unified (22 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 51 | `GET\|HEAD` | `eviron` | `eviron.index` | `EnverionsoneController@index` | `web`, `auth`, `permission:environment.view` | Unified EC master register (B1 + B2) |
| 52 | `GET\|HEAD` | `eviron/{id}` | `eviron.show` | `EnverionsoneController@show` | `web`, `auth`, `permission:environment.view` | Dynamic project dossier (`whereNumber: id`) |
| 53 | `GET\|HEAD` | `eviron/create` | `eviron.create` | `EnverionsoneController@create` | `web`, `auth`, `permission:environment.view` | Category intake wizard (B1-SC1 vs B2) |
| 54 | `GET\|HEAD` | `environstage1` | `environstage1` | `EnverionsoneController@index1` | `web`, `auth`, `permission:environment.view` | Backward-compatible SC1 redirect |
| 55 | `GET\|HEAD` | `environstage2` | `environstage2` | `EnverionsoneController@index2` | `web`, `auth`, `permission:environment.view` | Backward-compatible SC2 redirect |
| 56 | `GET\|HEAD` | `environment-b2` | `environment-b2.index` | `EnvironmentalB2Controller@index` | `web`, `auth`, `permission:environment.view` | B2 clearance master list |
| 57 | `GET\|HEAD` | `environment-b2/step/{step}` | `environment-b2.step` | `EnvironmentalB2Controller@wizard` | `web`, `auth`, `permission:environment.view` | B2 creation wizard (`whereNumber: step`) |
| 58 | `GET\|HEAD` | `environment-b2/{project}` | `environment-b2.show` | `EnvironmentalB2Controller@show` | `web`, `auth`, `permission:environment.view` | B2 project dossier & folder view |
| 59 | `GET\|HEAD` | `environment-b2/documents/{document}/download` | `environment-b2.documents.download` | `EnvironmentalB2Controller@download` | `web`, `auth`, `permission:environment.view` | Download B2 document file |
| 60 | `GET\|HEAD` | `eviron/documents/{document}/download` | `eviron.documents.download` | `EnverionsoneController@downloadDocument` | `web`, `auth`, `permission:environment.view` | Download unified document file |
| 61 | `POST` | `eviron` | `eviron.store` | `EnverionsoneController@store` | `web`, `auth`, `permission:environment.b2.create` | Store unified EC project (B1 or B2) |
| 62 | `POST` | `environment-b2` | `environment-b2.store` | `EnvironmentalB2Controller@store` | `web`, `auth`, `permission:environment.b2.create` | Legacy B2 project store |
| 63 | `POST` | `eviron/{id}/documents/{document}/upload` | `eviron.documents.upload` | `EnverionsoneController@uploadDocument` | `web`, `auth`, `permission:environment.b2.upload` | Upload file to unified EC slot |
| 64 | `POST` | `eviron/{id}/documents/add` | `eviron.documents.add` | `EnverionsoneController@addDocument` | `web`, `auth`, `permission:environment.b2.upload` | Add custom document to EC folder |
| 65 | `POST` | `environment-b2/documents/{document}/upload` | `environment-b2.documents.upload` | `EnvironmentalB2Controller@upload` | `web`, `auth`, `permission:environment.b2.upload` | Upload file to B2 checklist |
| 66 | `POST` | `eviron/{id}/status` | `eviron.status` | `EnverionsoneController@updateStatus` | `web`, `auth`, `permission:environment.b2.review` | Update overall EC project status |
| 67 | `POST` | `eviron/{id}/submit-sc1-ppt` | `eviron.submitSc1ToPpt` | `EnverionsoneController@submitSc1ToPpt` | `web`, `auth`, `permission:environment.b2.review` | Stage 1 Gate: Submit ToR to PPT |
| 68 | `POST` | `eviron/{id}/submit-sc2-ppt` | `eviron.submitSc2ToPpt` | `EnverionsoneController@submitSc2ToPpt` | `web`, `auth`, `permission:environment.b2.review` | Stage 2 Gate: Submit Final EC to PPT |
| 69 | `POST` | `eviron/{id}/documents/{document}/review` | `eviron.documents.review` | `EnverionsoneController@reviewDocument` | `web`, `auth`, `permission:environment.b2.review` | Approve or flag EC document |
| 70 | `POST` | `environment-b2/{project}/status` | `environment-b2.status` | `EnvironmentalB2Controller@updateStatus` | `web`, `auth`, `permission:environment.b2.review` | Update B2 project lifecycle status |
| 71 | `POST` | `environment-b2/documents/{document}/review` | `environment-b2.documents.review` | `EnvironmentalB2Controller@review` | `web`, `auth`, `permission:environment.b2.review` | Validate/approve B2 document |

---

### Category F: EC Certificate Issuance (5 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 72 | `GET\|HEAD` | `ec-certificate` | `ec-certificate.index` | `EcCertificateController@index` | `web`, `auth`, `permission:environment.view` | EC certificates master register |
| 73 | `GET\|HEAD` | `ec-certificate/{id}` | `ec-certificate.show` | `EcCertificateController@show` | `web`, `auth`, `permission:environment.view` | Printable official letterhead view (`whereNumber: id`) |
| 74 | `GET\|HEAD` | `ec-certificate/step/{step}` | `ec-certificate.step` | `EcCertificateController@wizard` | `web`, `auth`, `permission:environment.view` | 8-step gated issuance wizard (`whereNumber: step`) |
| 75 | `POST` | `ec-certificate/step/{step}` | `ec-certificate.saveStep` | `EcCertificateController@saveStep` | `web`, `auth`, `permission:environment.view` | Persist issuance wizard step (`whereNumber: step`) |
| 76 | `POST` | `ec-certificate` | `ec-certificate.store` | `EcCertificateController@store` | `web`, `auth`, `permission:environment.view` | Commit and issue official certificate |

---

### Category G: PPT Department (7 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 77 | `GET\|HEAD` | `ppt-department` | `ppt-department.index` | `PptDepartmentController@index` | `web`, `auth`, `permission:ppt.view` | Presentation applications register |
| 78 | `GET\|HEAD` | `ppt-department/step/{step}` | `ppt-department.step` | `PptDepartmentController@wizard` | `web`, `auth`, `permission:ppt.view` | 9-step PPT intake wizard (`whereNumber: step`) |
| 79 | `POST` | `ppt-department/step/{step}` | `ppt-department.saveStep` | `PptDepartmentController@saveStep` | `web`, `auth`, `permission:ppt.view` | Save PPT wizard draft step (`whereNumber: step`) |
| 80 | `POST` | `ppt-department` | `ppt-department.store` | `PptDepartmentController@store` | `web`, `auth`, `permission:ppt.view` | Finalize PPT application |
| 81 | `GET\|HEAD` | `ppt-department/{id}` | `ppt-department.show` | `PptDepartmentController@show` | `web`, `auth`, `permission:ppt.view` | Presentation dossier (`whereNumber: id`) |
| 82 | `POST` | `ppt-department/{id}/approve-stage` | `ppt-department.approveStage` | `PptDepartmentController@approvePresentation` | `web`, `auth`, `permission:ppt.view` | Statutory Gate Approval (Advances ToR or Final EC) |
| 83 | `POST` | `ppt-department/upload` | `ppt-department.upload` | `PptDepartmentController@uploadDocument` | `web`, `auth`, `permission:ppt.view` | Upload presentation slide/annexure |

---

### Category H: DGPS Boundary Survey (6 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 84 | `GET\|HEAD` | `dgps-survey` | `dgps-survey.index` | `DgpsSurveyController@index` | `web`, `auth`, `permission:dgps.view` | DGPS survey register & KPI bar |
| 85 | `GET\|HEAD` | `dgps-survey/step/{step}` | `dgps-survey.step` | `DgpsSurveyController@wizard` | `web`, `auth`, `permission:dgps.view` | 8-step survey wizard (`whereNumber: step`) |
| 86 | `POST` | `dgps-survey/step/{step}` | `dgps-survey.saveStep` | `DgpsSurveyController@saveStep` | `web`, `auth`, `permission:dgps.view` | Save survey wizard step (`whereNumber: step`) |
| 87 | `POST` | `dgps-survey` | `dgps-survey.store` | `DgpsSurveyController@store` | `web`, `auth`, `permission:dgps.view` | Commit survey record & numbering |
| 88 | `GET\|HEAD` | `dgps-survey/{id}` | `dgps-survey.show` | `DgpsSurveyController@show` | `web`, `auth`, `permission:dgps.view` | Survey dossier & coordinate sheets (`whereNumber: id`) |
| 89 | `POST` | `dgps-survey/upload` | `dgps-survey.upload` | `DgpsSurveyController@uploadDocument` | `web`, `auth`, `permission:dgps.view` | Upload raw RINEX / KML / CSV data |

---

### Category I: Drone Volumetric Survey (2 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 90 | `GET\|HEAD` | `drone-survey` | `drone-survey.index` | `DroneSurveyController@index` | `web`, `auth`, `permission:drone.view` | Drone survey register |
| 91 | `GET\|HEAD` | `drone-survey/step/{step}` | `drone-survey.step` | `DroneSurveyController@wizard` | `web`, `auth`, `permission:drone.view` | 8-step drone intake wizard (`whereNumber: step`) |

---

### Category J: EC Half-Yearly Compliance Monitoring (6 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 92 | `GET\|HEAD` | `ec-compliance` | `ec-compliance.index` | `EcComplianceController@index` | `web`, `auth`, `permission:environment.view` | Compliance register & KPIs |
| 93 | `GET\|HEAD` | `ec-compliance/step/{step}` | `ec-compliance.step` | `EcComplianceController@wizard` | `web`, `auth`, `permission:environment.view` | 8-step compliance wizard (`whereNumber: step`) |
| 94 | `POST` | `ec-compliance/step/{step}` | `ec-compliance.saveStep` | `EcComplianceController@saveStep` | `web`, `auth`, `permission:environment.view` | Save compliance draft step (`whereNumber: step`) |
| 95 | `POST` | `ec-compliance` | `ec-compliance.store` | `EcComplianceController@store` | `web`, `auth`, `permission:environment.view` | Commit compliance record & numbering |
| 96 | `GET\|HEAD` | `ec-compliance/{id}` | `ec-compliance.show` | `EcComplianceController@show` | `web`, `auth`, `permission:environment.view` | Compliance dossier (`whereNumber: id`) |
| 97 | `POST` | `ec-compliance/upload` | `ec-compliance.upload` | `EcComplianceController@uploadDocument` | `web`, `auth`, `permission:environment.view` | Upload 19 documents / 4 lab reports |

---

### Category K: User Management (4 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 98 | `GET\|HEAD` | `user` | `user.index` | `UserController@index` | `web`, `auth`, `permission:users.view` | Staff users register |
| 99 | `POST` | `useradd` | `useradd` | `UserController@store` | `web`, `auth`, `permission:users.create` | Create user with avatar & User Code |
| 100 | `POST` | `useredit` | `useredit` | `UserController@update` | `web`, `auth`, `permission:users.edit` | Update user profile & role |
| 101 | `POST` | `userdelete` | `userdelete` | `UserController@destroy` | `web`, `auth`, `permission:users.delete` | Delete user with last admin guard |

---

### Category L: Roles & Permissions (5 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 102 | `GET\|HEAD` | `roles` | `roles.index` | `RolesController@index` | `web`, `auth`, `permission:roles.view` | Roles master table & module matrix |
| 103 | `GET\|HEAD` | `roles/{id}/permissions` | `roles.permissions` | `RolesController@getPermissions` | `web`, `auth`, `permission:roles.view` | AJAX fetch assigned permissions |
| 104 | `POST` | `roleadd` | `roleadd` | `RolesController@store` | `web`, `auth`, `permission:roles.create` | Create new Spatie role |
| 105 | `POST` | `roleupdate` | `roleupdate` | `RolesController@update` | `web`, `auth`, `permission:roles.edit` | Sync permissions & update role |
| 106 | `POST` | `roledelete` | `roledelete` | `RolesController@destroy` | `web`, `auth`, `permission:roles.delete` | Delete non-admin role |

---

### Category M: Branches & Department Masters (4 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 107 | `GET\|HEAD` | `branch` | `branch.index` | `BranchController@index` | `web`, `auth`, `permission:branch.view` | Branch / Department listing |
| 108 | `POST` | `branchadd` | `branchadd` | `BranchController@store` | `web`, `auth`, `permission:branch.create` | Add branch |
| 109 | `POST` | `branchedit` | `branchedit` | `BranchController@update` | `web`, `auth`, `permission:branch.edit` | Edit branch |
| 110 | `POST` | `branchdelete` | `branchdelete` | `BranchController@destroy` | `web`, `auth`, `permission:branch.delete` | Delete branch |

---

### Category N: Category Master (4 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 111 | `GET\|HEAD` | `category` | `category.index` | `CategoryController@index` | `web`, `auth`, `permission:category.view` | Product/mineral categories list |
| 112 | `POST` | `categoryadd` | `categoryadd` | `CategoryController@store` | `web`, `auth`, `permission:category.create` | Add category |
| 113 | `POST` | `categoryedit` | `categoryedit` | `CategoryController@update` | `web`, `auth`, `permission:category.edit` | Update category |
| 114 | `POST` | `categorydelete` | `categorydelete` | `CategoryController@destroy` | `web`, `auth`, `permission:category.delete` | Soft-delete category |

---

### Category O: Unit Master (1 Route)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 115 | `GET\|HEAD` | `unit` | `unit.index` | `UnitController@index` | `web`, `auth`, `permission:unit.view` | Measurement units list |

---

### Category P: Product & Stock Masters (3 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 116 | `GET\|HEAD` | `product` | `product.index` | `ProductController@index` | `web`, `auth`, `permission:product.view` | Product master listing |
| 117 | `POST` | `productadd` | `productadd` | `ProductController@store` | `web`, `auth`, `permission:product.create` | Add product with barcode & stock |
| 118 | `GET\|HEAD` | `productstock` | `productstock.index` | `ProductStockController@index` | `web`, `auth`, `permission:product.view` | Product inventory register |

---

### Category Q: Framework, Storage & Health (3 Routes)

| # | HTTP Method | URI | Route Name | Action Controller & Method | Middleware | Description / Access |
|---|-------------|-----|------------|----------------------------|------------|----------------------|
| 119 | `GET\|HEAD` | `up` | *None* | `Closure` (Health check) | *None* | Laravel 12 application health check |
| 120 | `GET\|HEAD` | `storage/{path}` | `storage.local` | `Closure` (Storage link) | *None* | Local disk asset delivery stream |
| 121 | `PUT` | `storage/{path}` | `storage.local.upload` | `Closure` (Storage upload) | *None* | Local disk upload stream |

---

## 3. Route Summary by HTTP Verb

| HTTP Verb | Count |
|-----------|-------|
| `GET \| HEAD` | 61 |
| `POST` | 59 |
| `PUT` | 1 |
| **Total** | **121** |

---
*End of Document 06 — Web Routing Architecture & Route Catalog*

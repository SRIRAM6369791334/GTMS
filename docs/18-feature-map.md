# GTMS — End-to-End Feature Map & Architectural Matrix

**Project Name:** Granite / Mining Tracking Management System (GTMS)  
**Domain:** District Mining Office Management & Statutory Regulatory ERP  
**State / Region:** Tamil Nadu, India  
**Target Architecture:** Monolithic Laravel 12 Enterprise Application  
**Authoritative Source:** Codebase Inspection & Empirical Routing Table Audit  
**Document Number:** `18` of `23`

---

## 1. Executive Summary

This document provides the authoritative **End-to-End Feature Matrix** for the GTMS enterprise application. It establishes the direct structural traceability connecting user-facing features to their underlying Laravel components:

$$\text{Feature} \longrightarrow \text{Route / HTTP Verb} \longrightarrow \text{Controller@Method} \longrightarrow \text{Eloquent Models} \longrightarrow \text{Database Tables} \longrightarrow \text{Blade View / JSON}$$

Every operational module across the 121 registered routes in `routes/web.php` is cataloged below, along with the enforced Spatie RBAC permission gate and multi-tenancy `BranchScope` applicability.

---

## 2. Module 1: Authentication & Session Management

| Feature | HTTP Route & Name | Controller@Method | Eloquent Models | Database Tables | Blade View / Response | Permission Guard |
|---|---|---|---|---|---|---|
| **Login View** | `GET /`<br>`GET /login`<br>`name('login')` | `AuthController@showLogin` | None | `sessions` | `resources/views/pages/login.blade.php` | `guest` |
| **Login Authentication** | `POST /login`<br>`name('login.post')` | `AuthController@login` | `User` | `users`, `sessions` | Redirect to `/dashboard` or back with errors | `guest` |
| **System Logout** | `POST /logout`<br>`name('logout')` | `AuthController@logout` | `User` | `sessions` | Redirect to `/login` | `auth` |

---

## 3. Module 2: Executive Dashboard & Operations Hub

| Feature | HTTP Route & Name | Controller@Method | Eloquent Models | Database Tables | Blade View / Response | Permission Guard |
|---|---|---|---|---|---|---|
| **Executive Operations Dashboard** | `GET /dashboard`<br>`name('dashboard')` | Closure | `LeaseApplication`, `MiningApplication`, `EnvironmentProject`, `Customer` | `lease_applications`, `mining_applications`, `environment_projects`, `customers` | `resources/views/pages/index.blade.php` | `auth` |

---

## 4. Module 3: Customer Directory & Master Profiles

| Feature | HTTP Route & Name | Controller@Method | Eloquent Models | Database Tables | Blade View / Response | Permission Guard |
|---|---|---|---|---|---|---|
| **Customer Directory Listing** | `GET /customers`<br>`name('customers.index')` | `CustomerDirectoryController@index` | `Customer`, `District`, `Mineral` | `customers`, `districts`, `minerals` | `resources/views/pages/customers.blade.php` | `permission:customer.view` |
| **Customer Profile Dossier** | `GET /customers/{slug}`<br>`name('customers.show')` | `CustomerDirectoryController@show` | `Customer`, `District`, `Mineral`, `LeaseApplication` | `customers`, `districts`, `minerals`, `lease_applications` | `resources/views/pages/customer_profile.blade.php` | `permission:customer.view` |
| **Create New Customer** | `POST /customeradd`<br>`name('customeradd')` | `CustomerDirectoryController@store` | `Customer` | `customers` | JSON / Redirect to `/customers` with flash | `permission:customer.create` |
| **Update Customer Profile** | `POST /customeredit`<br>`name('customeredit')` | `CustomerDirectoryController@update` | `Customer` | `customers` | JSON / Redirect to `/customers` with flash | `permission:customer.edit` |
| **Delete Customer (Soft-Delete)** | `POST /customerdelete`<br>`name('customerdelete')` | `CustomerDirectoryController@destroy` | `Customer` | `customers` | JSON / Redirect to `/customers` with flash | `permission:customer.delete` |
| **MIMAS / Unique ID Lookup (AJAX)** | `GET /customers/lookup-mimas/{mimas_no}`<br>`name('customers.lookup.mimas')` | `CustomerDirectoryController@lookupByMimas` | `Customer`, `District`, `Mineral` | `customers`, `districts`, `minerals` | JSON `{ status: 1, customer: {...} }` | `auth` |

---

## 5. Module 4: Customer 360 Dossier & Commercial Invoicing

| Feature | HTTP Route & Name | Controller@Method | Eloquent Models | Database Tables | Blade View / Response | Permission Guard |
|---|---|---|---|---|---|---|
| **Customer 360 Master Index** | `GET /customer-tracking`<br>`name('customer-tracking.index')` | `CustomerTrackingController@index` | `Customer`, `District` + 7 Child Relations | `customers`, `districts`, `lease_applications`, `mining_applications`, `environment_projects`, etc. | `resources/views/pages/customer_tracking/index.blade.php` | `permission:customer.view` |
| **Live Universal Search (AJAX)** | `GET /customer-tracking/search`<br>`name('customer-tracking.search')` | `CustomerTrackingController@search` | `Customer` | `customers`, `lease_applications`, `mining_applications`, etc. | JSON `{ results: [ ... ] }` | `permission:customer.view` |
| **Customer 360 Full Dossier** | `GET /customer-tracking/{customer}`<br>`name('customer-tracking.show')` | `CustomerTrackingController@show` | `Customer` + All 7 Child Relations | `customers` and all 7 statutory project tables | `resources/views/pages/customer_tracking/show.blade.php` | `permission:customer.view` |
| **Consolidated Proforma Invoice** | `GET /customer-tracking/{customer}/proforma-invoice`<br>`name('customer-tracking.proforma-invoice')` | `CustomerTrackingController@proformaInvoice` | `Customer`, `MiningApplication`, `LeaseApplication`, `EnvironmentProject` | `customers`, `mining_applications`, `lease_applications`, `environment_projects`, `application_payments` | `resources/views/pages/customer_tracking/proforma_invoice.blade.php` | `permission:customer.view` |
| **Consolidated Tax Invoice** | `GET /customer-tracking/{customer}/tax-invoice`<br>`name('customer-tracking.tax-invoice')` | `CustomerTrackingController@taxInvoice` | `Customer`, `MiningApplication`, `LeaseApplication`, `EnvironmentProject` | `customers`, `mining_applications`, `lease_applications`, `environment_projects`, `application_payments` | `resources/views/pages/customer_tracking/tax_invoice.blade.php` | `permission:customer.view` |

---

## 6. Module 5: Lease Application Lifecycle (CustomerController)

| Feature | HTTP Route & Name | Controller@Method | Eloquent Models | Database Tables | Blade View / Response | Permission Guard |
|---|---|---|---|---|---|---|
| **Lease Application Register** | `GET /application`<br>`name('application.index')` | `CustomerController@index` | `LeaseApplication`, `Customer`, `District`, `Mineral` | `lease_applications`, `customers`, `districts`, `minerals` | `resources/views/pages/lease_application/index.blade.php` | `permission:application.view` |
| **Application Detail Dossier** | `GET /viewapplication`<br>`name('viewapplication')` | `CustomerController@viewApplication` | `LeaseApplication`, `Customer`, `LeaseDocument`, `ActivityLog` | `lease_applications`, `customers`, `lease_documents`, `activity_logs` | `resources/views/pages/lease_application/viewapplication.blade.php` | `permission:application.view` |
| **Wizard Step 1: Applicant Details** | `GET /step1`<br>`name('step1')` | `CustomerController@step1` | `District`, `Mineral`, `LeaseCategory` | `districts`, `minerals`, `lease_categories` | `resources/views/pages/lease_application/createstep1.blade.php` | `permission:application.create` |
| **Save Step 1** | `POST /step1`<br>`name('step1.save')` | `CustomerController@saveStep1` | None (Session Draft) | None (Session: `lease_draft.step1`) | Redirect to `/step2` | `permission:application.create` |
| **Wizard Step 2: Concession Area** | `GET /step2`<br>`name('step2')` | `CustomerController@step2` | `District`, `Mineral` | `districts`, `minerals` | `resources/views/pages/lease_application/createstep2.blade.php` | `permission:application.create` |
| **Save Step 2** | `POST /step2`<br>`name('step2.save')` | `CustomerController@saveStep2` | None (Session Draft) | None (Session: `lease_draft.step2`) | Redirect to `/step3` | `permission:application.create` |
| **Wizard Step 3: Land Ownership** | `GET /step3`<br>`name('step3')` | `CustomerController@step3` | None | None | `resources/views/pages/lease_application/createstep3.blade.php` | `permission:application.create` |
| **Save Step 3** | `POST /step3`<br>`name('step3.save')` | `CustomerController@saveStep3` | None (Session Draft) | None (Session: `lease_draft.step3`) | Redirect to `/step4` | `permission:application.create` |
| **Wizard Step 4: Documents Folder Checklist** | `GET /step4`<br>`name('step4')` | `CustomerController@step4` | `Folder`, `DocumentField` | `folders`, `document_fields` | `resources/views/pages/lease_application/createstep4.blade.php` | `permission:application.create` |
| **Wizard Step 5: Document Upload** | `GET /step5`<br>`name('step5')` | `CustomerController@step5` | `Folder`, `DocumentField` | `folders`, `document_fields` | `resources/views/pages/lease_application/createstep5.blade.php` | `permission:application.create` |
| **Upload Document (AJAX)** | `POST /step5/upload`<br>`name('step5.upload')` | `CustomerController@uploadDocument` | `LeaseDocument` | `lease_documents` | JSON `{ success: true, file_path: ... }` | `permission:application.create` |
| **Wizard Step 6: Handling Persons** | `GET /step6`<br>`name('step6')` | `CustomerController@step6` | None | None | `resources/views/pages/lease_application/createstep6.blade.php` | `permission:application.create` |
| **Save Step 6** | `POST /step6`<br>`name('step6.save')` | `CustomerController@saveStep6` | None (Session Draft) | None (Session: `lease_draft.handlers`) | Redirect to `/step7` | `permission:application.create` |
| **Wizard Step 7: Payment Details** | `GET /step7`<br>`name('step7')` | `CustomerController@step7` | None | None | `resources/views/pages/lease_application/createstep7.blade.php` | `permission:application.create` |
| **Save Step 7** | `POST /step7`<br>`name('step7.save')` | `CustomerController@saveStep7` | None (Session Draft) | None (Session: `lease_draft.payment`) | Redirect to `/step8` | `permission:application.create` |
| **Wizard Step 8: Final Review & Submit** | `GET /step8`<br>`name('step8')` | `CustomerController@step8` | None | None | `resources/views/pages/lease_application/createstep8.blade.php` | `permission:application.create` |
| **Submit Final Application** | `POST /application/submit`<br>`name('application.submit')` | `CustomerController@submit` | `LeaseApplication`, `LeaseDocument`, `MimasCredential`, `ApplicationHandler`, `ApplicationPayment`, `ActivityLog` | `lease_applications`, `lease_documents`, `mimas_credentials`, `application_handlers`, `application_payments`, `activity_logs` | Redirect to `/application` with flash | `permission:application.create` |
| **Resume Saved Application Draft** | `GET /application/{id}/resume`<br>`name('application.resume')` | `CustomerController@resumeDraft` | `LeaseApplication` | `lease_applications` | Redirect to appropriate step | `permission:application.create` |
| **Validate Lease Application** | `POST /application/{id}/validate`<br>`name('application.validate')` | `CustomerController@validateApplication` | `LeaseApplication`, `ActivityLog` | `lease_applications`, `activity_logs` | Redirect back with success | `permission:application.edit` |
| **Approve Lease Application** | `POST /application/{id}/approve`<br>`name('application.approve')` | `CustomerController@approveApplication` | `LeaseApplication`, `ActivityLog` | `lease_applications`, `activity_logs` | Redirect back with success | `permission:application.edit` |
| **Reject Lease Application** | `POST /application/{id}/reject`<br>`name('application.reject')` | `CustomerController@rejectApplication` | `LeaseApplication`, `ActivityLog` | `lease_applications`, `activity_logs` | Redirect back with success | `permission:application.edit` |
| **Transition: Move to Mining Plan** | `POST /application/{id}/move-to-mining`<br>`name('application.moveToMining')` | `CustomerController@moveToMining` | `LeaseApplication`, `MiningApplication`, `MiningDocument`, `ActivityLog` | `lease_applications`, `mining_applications`, `mining_documents`, `activity_logs` | Redirect to `/process?id={miningAppId}` | `permission:application.edit` |
| **Update Document Scrutiny Status** | `POST /application/document/{id}/status`<br>`name('application.document.status')` | `CustomerController@updateDocumentStatus` | `LeaseDocument`, `ActivityLog` | `lease_documents`, `activity_logs` | JSON `{ success: true }` | `permission:application.edit` |
| **Generate Statutory Lease Report** | `GET /application/{id}/report`<br>`name('application.report')` | `CustomerController@generateReport` | `LeaseApplication` | `lease_applications` | `resources/views/pages/lease_application/report.blade.php` | `permission:application.view` |

---

## 7. Module 6: Mining Plan Lifecycle (Process 6.1 – 6.6)

| Feature | HTTP Route & Name | Controller@Method | Eloquent Models | Database Tables | Blade View / Response | Permission Guard |
|---|---|---|---|---|---|---|
| **Mining Plan Register** | `GET /miningplan`<br>`name('miningplan.index')` | `MiningController@index` | `MiningApplication`, `Customer`, `District` | `mining_applications`, `customers`, `districts` | `resources/views/pages/mining-portal/index.blade.php` | `permission:mining.view` |
| **New Application Intake View** | `GET /newapplication`<br>`name('newapplication')` | `MiningController@newApplication` | `NatureOfWork`, `ApplicantType`, `PlanType`, `District`, `Mineral` | `nature_of_works`, `applicant_types`, `plan_types`, `districts`, `minerals` | `resources/views/pages/mining-portal/newapplication.blade.php` | `permission:mining.create` |
| **Store Mining Application** | `POST /newapplication`<br>`name('newapplication.store')` | `MiningController@store` | `MiningApplication`, `ApplicationHandler`, `ApplicationPayment`, `ActivityLog` | `mining_applications`, `mining_application_minerals`, `application_handlers`, `application_payments`, `activity_logs` | Redirect to `/projectfolder?id={id}` | `permission:mining.create` |
| **Project Folder Dossier** | `GET /projectfolder`<br>`name('projectfolder')` | `MiningController@projectFolder` | `MiningApplication`, `MiningDocument`, `Folder` | `mining_applications`, `mining_documents`, `folders` | `resources/views/pages/mining-portal/projectfolder.blade.php` | `permission:mining.view` |
| **Document Management View** | `GET /document`<br>`name('document')` | `MiningController@Document` | `MiningApplication`, `MiningDocument`, `Folder` | `mining_applications`, `mining_documents`, `folders` | `resources/views/pages/mining-portal/document.blade.php` | `permission:mining.view` |
| **Upload Mining Document** | `POST /mining/document/upload`<br>`name('mining.document.upload')` | `MiningController@uploadDocument` | `MiningDocument`, `ActivityLog` | `mining_documents`, `activity_logs` | JSON `{ success: true, document: ... }` | `permission:mining.create` |
| **Validate Mining Document** | `POST /mining/document/{id}/validate`<br>`name('mining.document.validate')` | `MiningController@validateDocument` | `MiningDocument`, `ActivityLog` | `mining_documents`, `activity_logs` | JSON `{ success: true }` | `permission:mining.edit` |
| **6-Stage Process Flow View** | `GET /process`<br>`name('process')` | `MiningController@Process` | `MiningApplication`, `ActivityLog` | `mining_applications`, `activity_logs` | `resources/views/pages/mining-portal/process.blade.php` | `permission:mining.view` |
| **Advance Mining Stage** | `POST /mining/application/{id}/stage`<br>`name('mining.application.stage')` | `MiningController@advanceStage` | `MiningApplication`, `ActivityLog` | `mining_applications`, `activity_logs` | Redirect back with success | `permission:mining.edit` |
| **Transition: Move to Environment** | `POST /mining/application/{id}/move-to-environment`<br>`name('mining.application.moveToEnvironment')` | `MiningController@moveToEnvironment` | `MiningApplication`, `EnvironmentProject`, `EnvironmentDocument`, `ActivityLog` | `mining_applications`, `environment_projects`, `environment_documents`, `activity_logs` | Redirect to `/eviron/{id}` | `permission:mining.edit` |

---

## 8. Module 7: Environmental Clearance (Unified B1 & B2)

| Feature | HTTP Route & Name | Controller@Method | Eloquent Models | Database Tables | Blade View / Response | Permission Guard |
|---|---|---|---|---|---|---|
| **Unified Environment Clearance Register** | `GET /eviron`<br>`name('eviron.index')` | `EnverionsoneController@index` | `EnvironmentProject`, `District`, `Customer`, `ActivityLog` | `environment_projects`, `districts`, `customers`, `activity_logs` | `resources/views/pages/eviron/index.blade.php` | `permission:environment.view` |
| **Project Details & Dynamic Folders** | `GET /eviron/{id}`<br>`name('eviron.show')` | `EnverionsoneController@show` | `EnvironmentProject`, `EnvironmentDocument`, `PptApplication` | `environment_projects`, `environment_documents`, `ppt_applications` | `resources/views/pages/eviron/show.blade.php` | `permission:environment.view` |
| **Intake Wizard (B1 / B2)** | `GET /eviron/create`<br>`name('eviron.create')` | `EnverionsoneController@create` | `Customer`, `District` | `customers`, `districts` | `resources/views/pages/eviron/create.blade.php` | `permission:environment.view` |
| **Store Unified Environment Project** | `POST /eviron`<br>`name('eviron.store')` | `EnverionsoneController@store` | `EnvironmentProject`, `ApplicationHandler`, `ApplicationPayment`, `ActivityLog` | `environment_projects`, `application_handlers`, `application_payments`, `activity_logs` | Redirect to `/eviron/{id}` | `permission:environment.b2.create` |
| **Upload Project Document** | `POST /eviron/{id}/documents/{document}/upload`<br>`name('eviron.documents.upload')` | `EnverionsoneController@uploadDocument` | `EnvironmentDocument` | `environment_documents` | JSON `{ success: true }` | `permission:environment.b2.upload` |
| **Add Custom Document Field** | `POST /eviron/{id}/documents/add`<br>`name('eviron.documents.add')` | `EnverionsoneController@addDocument` | `EnvironmentDocument` | `environment_documents` | JSON `{ success: true }` | `permission:environment.b2.upload` |
| **Review Project Document** | `POST /eviron/{id}/documents/{document}/review`<br>`name('eviron.documents.review')` | `EnverionsoneController@reviewDocument` | `EnvironmentDocument` | `environment_documents` | JSON `{ success: true }` | `permission:environment.b2.review` |
| **Download Document** | `GET /eviron/documents/{document}/download`<br>`name('eviron.documents.download')` | `EnverionsoneController@downloadDocument` | `EnvironmentDocument` | `environment_documents` | Binary File Download | `permission:environment.view` |
| **Update Overall Project Status** | `POST /eviron/{id}/status`<br>`name('eviron.status')` | `EnverionsoneController@updateStatus` | `EnvironmentProject`, `ActivityLog` | `environment_projects`, `activity_logs` | Redirect back with success | `permission:environment.b2.review` |
| **Submit SC1 to PPT Gate 1** | `POST /eviron/{id}/submit-sc1-ppt`<br>`name('eviron.submitSc1ToPpt')` | `EnverionsoneController@submitSc1ToPpt` | `EnvironmentProject`, `PptApplication`, `ActivityLog` | `environment_projects`, `ppt_applications`, `activity_logs` | Redirect back with success | `permission:environment.b2.review` |
| **Submit SC2 to PPT Gate 2** | `POST /eviron/{id}/submit-sc2-ppt`<br>`name('eviron.submitSc2ToPpt')` | `EnverionsoneController@submitSc2ToPpt` | `EnvironmentProject`, `PptApplication`, `ActivityLog` | `environment_projects`, `ppt_applications`, `activity_logs` | Redirect back with success | `permission:environment.b2.review` |
| **B2 Register (Legacy Compat)** | `GET /environment-b2`<br>`name('environment-b2.index')` | `EnvironmentalB2Controller@index` | `EnvironmentProject` | `environment_projects` | `resources/views/pages/environment/b2/index.blade.php` | `permission:environment.view` |
| **B2 Wizard (Legacy Compat)** | `GET /environment-b2/step/{step}`<br>`name('environment-b2.step')` | `EnvironmentalB2Controller@wizard` | `EnvironmentProject` | `environment_projects` | `resources/views/pages/environment/b2/wizard.blade.php` | `permission:environment.view` |
| **B2 Show (Legacy Compat)** | `GET /environment-b2/{project}`<br>`name('environment-b2.show')` | `EnvironmentalB2Controller@show` | `EnvironmentProject` | `environment_projects` | `resources/views/pages/environment/b2/show.blade.php` | `permission:environment.view` |

---

## 9. Module 8: EC Certificate Issuance

| Feature | HTTP Route & Name | Controller@Method | Eloquent Models | Database Tables | Blade View / Response | Permission Guard |
|---|---|---|---|---|---|---|
| **EC Certificate Register** | `GET /ec-certificate`<br>`name('ec-certificate.index')` | `EcCertificateController@index` | `EcCertificate`, `EnvironmentProject` | `ec_certificates`, `environment_projects` | `resources/views/pages/ec_certificate/index.blade.php` | `permission:environment.view` |
| **Official Letterhead Certificate View** | `GET /ec-certificate/{id}`<br>`name('ec-certificate.show')` | `EcCertificateController@show` | `EcCertificate`, `EnvironmentProject` | `ec_certificates`, `environment_projects` | `resources/views/pages/ec_certificate/show.blade.php` | `permission:environment.view` |
| **6-Step Stepper Wizard View** | `GET /ec-certificate/step/{step}`<br>`name('ec-certificate.step')` | `EcCertificateController@wizard` | `EnvironmentProject` | `environment_projects` | `resources/views/pages/ec_certificate/wizard.blade.php` | `permission:environment.view` |
| **Save Wizard Step (Session Draft)** | `POST /ec-certificate/step/{step}`<br>`name('ec-certificate.saveStep')` | `EcCertificateController@saveStep` | None (Session Draft) | None (Session: `ec_wizard.step*`) | Redirect to next step | `permission:environment.view` |
| **Store & Finalize Certificate** | `POST /ec-certificate`<br>`name('ec-certificate.store')` | `EcCertificateController@store` | `EcCertificate`, `EnvironmentProject`, `ActivityLog` | `ec_certificates`, `environment_projects`, `activity_logs` | Redirect to `/ec-certificate` | `permission:environment.view` |

---

## 10. Module 9: PPT Department (Presentation & Appraisal Gates)

| Feature | HTTP Route & Name | Controller@Method | Eloquent Models | Database Tables | Blade View / Response | Permission Guard |
|---|---|---|---|---|---|---|
| **PPT Department Register** | `GET /ppt-department`<br>`name('ppt-department.index')` | `PptDepartmentController@index` | `PptApplication`, `Customer`, `EnvironmentProject` | `ppt_applications`, `customers`, `environment_projects` | `resources/views/pages/ppt_department/index.blade.php` | `permission:ppt.view` |
| **PPT Presentation Dossier** | `GET /ppt-department/{id}`<br>`name('ppt-department.show')` | `PptDepartmentController@show` | `PptApplication`, `PptDocument`, `PptAgenda` | `ppt_applications`, `ppt_documents`, `ppt_agendas` | `resources/views/pages/ppt_department/show.blade.php` | `permission:ppt.view` |
| **PPT 9-Step Wizard View** | `GET /ppt-department/step/{step}`<br>`name('ppt-department.step')` | `PptDepartmentController@wizard` | `Customer`, `EnvironmentProject` | `customers`, `environment_projects` | `resources/views/pages/ppt_department/wizard.blade.php` | `permission:ppt.view` |
| **Save PPT Wizard Step** | `POST /ppt-department/step/{step}`<br>`name('ppt-department.saveStep')` | `PptDepartmentController@saveStep` | None (Session Draft) | None (Session: `ppt_wizard.step*`) | Redirect to next step | `permission:ppt.view` |
| **Store PPT Application** | `POST /ppt-department`<br>`name('ppt-department.store')` | `PptDepartmentController@store` | `PptApplication`, `ApplicationHandler`, `ApplicationPayment` | `ppt_applications`, `application_handlers`, `application_payments` | Redirect to `/ppt-department/{id}` | `permission:ppt.view` |
| **Approve Stage Gate (ToR / EC)** | `POST /ppt-department/{id}/approve-stage`<br>`name('ppt-department.approveStage')` | `PptDepartmentController@approvePresentation` | `PptApplication`, `EnvironmentProject`, `ActivityLog` | `ppt_applications`, `environment_projects`, `activity_logs` | Redirect back with success | `permission:ppt.view` |
| **Upload PPT Presentation Slide Deck** | `POST /ppt-department/upload`<br>`name('ppt-department.upload')` | `PptDepartmentController@uploadDocument` | `PptDocument` | `ppt_documents` | JSON `{ success: true }` | `permission:ppt.view` |

---

## 11. Module 10: DGPS Boundary Survey

| Feature | HTTP Route & Name | Controller@Method | Eloquent Models | Database Tables | Blade View / Response | Permission Guard |
|---|---|---|---|---|---|---|
| **DGPS Survey Register** | `GET /dgps-survey`<br>`name('dgps-survey.index')` | `DgpsSurveyController@index` | `DgpsSurvey`, `Customer`, `LeaseApplication` | `dgps_surveys`, `customers`, `lease_applications` | `resources/views/pages/dgps_survey/index.blade.php` | `permission:dgps.view` |
| **DGPS Survey Dossier** | `GET /dgps-survey/{id}`<br>`name('dgps-survey.show')` | `DgpsSurveyController@show` | `DgpsSurvey`, `DgpsPoint`, `DgpsDocument` | `dgps_surveys`, `dgps_points`, `dgps_documents` | `resources/views/pages/dgps_survey/show.blade.php` | `permission:dgps.view` |
| **DGPS 8-Step Wizard View** | `GET /dgps-survey/step/{step}`<br>`name('dgps-survey.step')` | `DgpsSurveyController@wizard` | `Customer`, `LeaseApplication` | `customers`, `lease_applications` | `resources/views/pages/dgps_survey/wizard.blade.php` | `permission:dgps.view` |
| **Save DGPS Wizard Step** | `POST /dgps-survey/step/{step}`<br>`name('dgps-survey.saveStep')` | `DgpsSurveyController@saveStep` | None (Session Draft) | None (Session: `dgps_wizard.step*`) | Redirect to next step | `permission:dgps.view` |
| **Store DGPS Survey Record** | `POST /dgps-survey`<br>`name('dgps-survey.store')` | `DgpsSurveyController@store` | `DgpsSurvey`, `DgpsPoint`, `ApplicationHandler`, `ApplicationPayment` | `dgps_surveys`, `dgps_points`, `application_handlers`, `application_payments` | Redirect to `/dgps-survey/{id}` | `permission:dgps.view` |
| **Upload DGPS Deliverables (CAD/KML)** | `POST /dgps-survey/upload`<br>`name('dgps-survey.upload')` | `DgpsSurveyController@uploadDocument` | `DgpsDocument` | `dgps_documents` | JSON `{ success: true }` | `permission:dgps.view` |

---

## 12. Module 11: Drone Aerial Survey Tracking

| Feature | HTTP Route & Name | Controller@Method | Eloquent Models | Database Tables | Blade View / Response | Permission Guard |
|---|---|---|---|---|---|---|
| **Drone Survey Register** | `GET /drone-survey`<br>`name('drone-survey.index')` | `DroneSurveyController@index` | `DroneSurvey`, `Customer` | `drone_surveys`, `customers` | `resources/views/pages/drone_survey/index.blade.php` | `permission:drone.view` |
| **Drone 8-Step Wizard View** | `GET /drone-survey/step/{step}`<br>`name('drone-survey.step')` | `DroneSurveyController@wizard` | `Customer` | `customers` | `resources/views/pages/drone_survey/wizard.blade.php` | `permission:drone.view` |

---

## 13. Module 12: EC Half-Yearly Compliance Monitoring

| Feature | HTTP Route & Name | Controller@Method | Eloquent Models | Database Tables | Blade View / Response | Permission Guard |
|---|---|---|---|---|---|---|
| **Compliance Register** | `GET /ec-compliance`<br>`name('ec-compliance.index')` | `EcComplianceController@index` | `EcCompliance`, `Customer` | `ec_compliances`, `customers` | `resources/views/pages/ec_compliance/index.blade.php` | `permission:environment.view` |
| **Compliance Dossier (19 Folders)** | `GET /ec-compliance/{id}`<br>`name('ec-compliance.show')` | `EcComplianceController@show` | `EcCompliance`, `EcComplianceDocument` | `ec_compliances`, `ec_compliance_documents` | `resources/views/pages/ec_compliance/show.blade.php` | `permission:environment.view` |
| **Compliance 8-Step Wizard View** | `GET /ec-compliance/step/{step}`<br>`name('ec-compliance.step')` | `EcComplianceController@wizard` | `Customer` | `customers` | `resources/views/pages/ec_compliance/wizard.blade.php` | `permission:environment.view` |
| **Save Compliance Wizard Step** | `POST /ec-compliance/step/{step}`<br>`name('ec-compliance.saveStep')` | `EcComplianceController@saveStep` | None (Session Draft) | None (Session: `compliance_wizard.step*`) | Redirect to next step | `permission:environment.view` |
| **Store Compliance Filing** | `POST /ec-compliance`<br>`name('ec-compliance.store')` | `EcComplianceController@store` | `EcCompliance`, `EcComplianceDocument`, `ApplicationHandler`, `ApplicationPayment` | `ec_compliances`, `ec_compliance_documents`, `application_handlers`, `application_payments` | Redirect to `/ec-compliance/{id}` | `permission:environment.view` |
| **Upload Compliance Document** | `POST /ec-compliance/upload`<br>`name('ec-compliance.upload')` | `EcComplianceController@uploadDocument` | `EcComplianceDocument` | `ec_compliance_documents` | JSON `{ success: true }` | `permission:environment.view` |

---

## 14. Module 13: System Administration, RBAC & Master Catalogs

| Feature | HTTP Route & Name | Controller@Method | Eloquent Models | Database Tables | Blade View / Response | Permission Guard |
|---|---|---|---|---|---|---|
| **Role Master Table** | `GET /roles`<br>`name('roles.index')` | `RolesController@index` | Spatie `Role`, `Permission` | `roles`, `permissions` | `resources/views/pages/roles.blade.php` | `permission:roles.view` |
| **Fetch Role Permissions (AJAX)** | `GET /roles/{id}/permissions`<br>`name('roles.permissions')` | `RolesController@getPermissions` | Spatie `Role` | `roles`, `role_has_permissions` | JSON `{ permissions: [ ... ] }` | `permission:roles.view` |
| **Create Role** | `POST /roleadd`<br>`name('roleadd')` | `RolesController@store` | Spatie `Role` | `roles`, `role_has_permissions` | Redirect back with success | `permission:roles.create` |
| **Update Role & Permissions** | `POST /roleupdate`<br>`name('roleupdate')` | `RolesController@update` | Spatie `Role` | `roles`, `role_has_permissions` | Redirect back with success | `permission:roles.edit` |
| **Delete Role** | `POST /roledelete`<br>`name('roledelete')` | `RolesController@destroy` | Spatie `Role` | `roles` | Redirect back with success | `permission:roles.delete` |
| **User Directory** | `GET /user`<br>`name('user.index')` | `UserController@index` | `User`, `Branch`, Spatie `Role` | `users`, `branches`, `roles` | `resources/views/pages/user.blade.php` | `permission:users.view` |
| **Create User** | `POST /useradd`<br>`name('useradd')` | `UserController@store` | `User` | `users`, `model_has_roles` | Redirect back with success | `permission:users.create` |
| **Update User Profile & Role** | `POST /useredit`<br>`name('useredit')` | `UserController@update` | `User` | `users`, `model_has_roles` | Redirect back with success | `permission:users.edit` |
| **Delete User** | `POST /userdelete`<br>`name('userdelete')` | `UserController@destroy` | `User` | `users` | Redirect back with success | `permission:users.delete` |
| **Branch Master Directory** | `GET /branch`<br>`name('branch.index')` | `BranchController@index` | `Branch` | `branches` | `resources/views/pages/branch.blade.php` | `permission:branch.view` |
| **Create Branch** | `POST /branchadd`<br>`name('branchadd')` | `BranchController@store` | `Branch` | `branches` | Redirect back with success | `permission:branch.create` |
| **Update Branch** | `POST /branchedit`<br>`name('branchedit')` | `BranchController@update` | `Branch` | `branches` | Redirect back with success | `permission:branch.edit` |
| **Delete Branch** | `POST /branchdelete`<br>`name('branchdelete')` | `BranchController@destroy` | `Branch` | `branches` | Redirect back with success | `permission:branch.delete` |
| **Category Catalog** | `GET /category`<br>`name('category.index')` | `CategoryController@index` | `Category` | `categories` | `resources/views/pages/category.blade.php` | `permission:category.view` |
| **Create Category** | `POST /categoryadd`<br>`name('categoryadd')` | `CategoryController@store` | `Category` | `categories` | Redirect back with success | `permission:category.create` |
| **Update Category** | `POST /categoryedit`<br>`name('categoryedit')` | `CategoryController@update` | `Category` | `categories` | Redirect back with success | `permission:category.edit` |
| **Delete Category** | `POST /categorydelete`<br>`name('categorydelete')` | `CategoryController@destroy` | `Category` | `categories` | Redirect back with success | `permission:category.delete` |
| **Measurement Units** | `GET /unit`<br>`name('unit.index')` | `UnitController@index` | `Unit` | `units` | `resources/views/pages/unit.blade.php` | `permission:unit.view` |
| **Product Master** | `GET /product`<br>`name('product.index')` | `ProductController@index` | `Product`, `Category`, `Unit` | `products`, `categories`, `units` | `resources/views/pages/product.blade.php` | `permission:product.view` |
| **Create Product** | `POST /productadd`<br>`name('productadd')` | `ProductController@store` | `Product` | `products` | Redirect back with success | `permission:product.create` |
| **Product Stock Master** | `GET /productstock`<br>`name('productstock.index')` | `ProductStockController@index` | `Product`, `MineralStockpile` | `products`, `mineral_stockpiles` | `resources/views/pages/productstock.blade.php` | `permission:product.view` |

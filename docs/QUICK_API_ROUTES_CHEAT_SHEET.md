# ⚡ GTMS Quick API & Routes Cheat Sheet
> **Token-Saver Reference:** Consult this file instead of parsing `routes/web.php` (43KB). Contains all system endpoints, controllers, methods, permissions, and request/response specifications.

---

## 1. Authentication & System Health

| Method | URI | Route Name | Controller Action | Permission Gate | Request Payload | Response Type |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/` or `/login` | `login` | `AuthController@showLogin` | `guest` | None | Blade: `pages.login` |
| `POST` | `/login` | `login.post` | `AuthController@login` | `guest` + `throttle:15,1` | `email` (accepts email or user_code), `password`, `remember` | Redirect -> `/dashboard` |
| `POST` | `/logout` | `logout` | `AuthController@logout` | `auth` | None | Redirect -> `/login` |
| `GET` | `/system/version` | `system.version` | Closure | Public | None | JSON status |
| `GET` | `/dashboard` | `dashboard` | Closure | `permission:dashboard.view` | None | Blade: `pages.index` |
| `GET` | `/notifications` | `notifications.index` | `NotificationController@index` | `auth` | None | Blade: `pages.notifications.index` |
| `POST` | `/notifications/{id}/mark-as-read` | `notifications.mark-as-read` | `NotificationController@markAsRead` | `auth` | None | JSON `{status: 1}` |
| `DELETE`| `/notifications/clear-all` | `notifications.clear-all` | `NotificationController@clearAll` | `auth` | None | JSON `{status: 1}` |

---

## 2. Authentication & Administrative Entities (User, Role, Branch)

| Method | URI | Route Name | Controller Action | Permission Gate | Request Payload | Response Type |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/user` | `user.index` | `UserController@index` | `permission:users.view` | None | Blade: `pages.authentication.users.index` |
| `POST` | `/useradd` | `useradd` | `UserController@store` | `permission:users.create` | `name`, `email`, `user_code`, `password`, `role_id`, `branch_id`, `mobile_num`, `status`, `image` | JSON `{status: 1, message, data}` |
| `POST` | `/useredit` | `useredit` | `UserController@update` | `permission:users.edit` | `id`, `name`, `email`, `user_code`, `password`, `role_id`, `branch_id`, `mobile_num`, `status`, `image` | JSON `{status: 1, message, data}` |
| `POST` | `/userdelete` | `userdelete` | `UserController@destroy` | `permission:users.delete` | `id` | JSON `{status: 1|0, message}` |
| `GET` | `/roles` | `roles.index` | `RolesController@index` | `permission:roles.view` | None | Blade: `pages.authentication.roles.index` |
| `GET` | `/roles/{id}/permissions` | `roles.permissions` | `RolesController@getPermissions` | `permission:roles.view` | URL `{id}` | JSON `{status: 1, role, permissions: []}` |
| `POST` | `/roleadd` | `roleadd` | `RolesController@store` | `permission:roles.create` | `name`, `permissions[]` | JSON `{status: 1, message, data}` |
| `POST` | `/roleupdate` | `roleupdate` | `RolesController@update` | `permission:roles.edit` | `id`, `name`, `permissions[]` | JSON `{status: 1, message, data}` |
| `POST` | `/roledelete` | `roledelete` | `RolesController@destroy` | `permission:roles.delete` | `id` | JSON `{status: 1|0, message}` |
| `GET` | `/branch` | `branch.index` | `BranchController@index` | `permission:branch.view` | None | Blade: `pages.authentication.branch.index` |
| `POST` | `/branchadd` | `branchadd` | `BranchController@store` | `permission:branch.create` | `branch_name`, `contact_person`, `mobile`, `address`, `city`, `state`, `pincode` | JSON `{status: 1, message, data}` |
| `POST` | `/branchedit` | `branchedit` | `BranchController@update` | `permission:branch.edit` | `id`, `branch_name`, `contact_person`, `mobile`, `address`, `city`, `state`, `pincode`, `status` | JSON `{status: 1, message, data}` |
| `POST` | `/branchdelete` | `branchdelete` | `BranchController@destroy` | `permission:branch.delete` | `id` | JSON `{status: 1|0, message}` |

---

## 3. Customers & Master Catalog

| Method | URI | Route Name | Controller Action | Permission Gate | Request Payload | Response Type |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/customers` | `customers.index` | `CustomerDirectoryController@index` | `permission:customer.view` | None | Blade: `pages.customers` |
| `GET` | `/customers/{slug}` | `customers.show` | `CustomerDirectoryController@show` | `permission:customer.view` | URL `{slug}` | Blade: `pages.customer_show` |
| `GET` | `/customers/lookup-mimas/{mimas_no}` | `customers.lookup.mimas` | `CustomerDirectoryController@lookupByMimas` | `permission:customer.view` | URL `{mimas_no}` | JSON `{status: 1, customer: {...}}` |
| `POST` | `/customeradd` | `customeradd` | `CustomerDirectoryController@store` | `permission:customer.create` | `customer_name`, `company_name`, `mimas_no`, `mobile_num`, `email`, `district_id`, `mineral_id`, `pan`, `aadhaar_no`, `gstin`, `area`, `address`, `status` | JSON `{status: 1, message, data}` |
| `POST` | `/customeredit` | `customeredit` | `CustomerDirectoryController@update` | `permission:customer.edit` | `id`, plus customer fields | JSON `{status: 1, message, data}` |
| `POST` | `/customerdelete` | `customerdelete` | `CustomerDirectoryController@destroy` | `permission:customer.delete` | `id` | JSON `{status: 1|0, message}` |
| `GET` | `/customer-tracking` | `customer-tracking.index` | `CustomerTrackingController@index` | `permission:customer.view` | Query filters | Blade: `pages.customer_tracking.index` |
| `GET` | `/customer-tracking/{customer}/proforma-invoice` | `customer-tracking.proforma-invoice` | `CustomerTrackingController@proformaInvoice`| `permission:customer.view` | URL `{customer}` | Blade / Print View |
| `GET` | `/customer-tracking/{customer}/tax-invoice` | `customer-tracking.tax-invoice` | `CustomerTrackingController@taxInvoice` | `permission:customer.view` | URL `{customer}` | Blade / Print View |
| `GET` | `/category` | `category.index` | `CategoryController@index` | `permission:category.view` | None | Blade: `pages.master.category.index` |
| `POST` | `/categoryadd` | `categoryadd` | `CategoryController@store` | `permission:category.create` | `category_name`, `status` | JSON `{status: 1}` |
| `GET` | `/unit` | `unit.index` | `UnitController@index` | `permission:unit.view` | None | Blade: `pages.master.unit.index` |
| `GET` | `/product` | `product.index` | `ProductController@index` | `permission:product.view` | None | Blade: `pages.master.product.index` |
| `POST` | `/productadd` | `productadd` | `ProductController@store` | `permission:product.create` | `product_name`, `category_id`, `unit_id`, `price` | JSON `{status: 1}` |
| `GET` | `/productstock` | `productstock.index` | `ProductStockController@index` | `permission:product.view` | None | Blade: `pages.master.productstock.index` |

---

## 4. Statutory Lease Application Wizard & Dossier

| Method | URI | Route Name | Controller Action | Permission Gate | Request Payload | Response Type |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/application` | `application.index` | `CustomerController@index` | `permission:application.view` | Filter queries | Blade: `pages.lease_application.customer` |
| `GET` | `/viewapplication` | `viewapplication` | `CustomerController@viewApplication` | `permission:application.view` | `id` query parameter | Blade: `pages.lease_application.viewapplication` |
| `GET` | `/application/{id}/report` | `application.report` | `CustomerController@generateReport` | `permission:application.view` | URL `{id}` | PDF / Printable report view |
| `GET` | `/step1` | `step1` | `CustomerController@step1` | `permission:application.create` | Draft session / ID | Blade: `createstep1` |
| `POST` | `/step1` | `step1.save` | `CustomerController@saveStep1` | `permission:application.create` | `customer_id`, `district_id`, `mineral_id`, `applicant_type_id`, `nature_of_work_id`, `application_date`, `area` | JSON / Redirect -> `/step2` |
| `GET` | `/step2` | `step2` | `CustomerController@step2` | `permission:application.create` | None | Blade: `createstep2` |
| `POST` | `/step2` | `step2.save` | `CustomerController@saveStep2` | `permission:application.create` | `mimas_username`, `mimas_password` (encrypted on save) | Redirect -> `/step3` |
| `GET` | `/step3` | `step3` | `CustomerController@step3` | `permission:application.create` | None | Blade: `createstep3` |
| `POST` | `/step3` | `step3.save` | `CustomerController@saveStep3` | `permission:application.create` | `lease_category_id` OR `other_rule_name` | Redirect -> `/step4` |
| `GET` | `/step4` | `step4` | `CustomerController@step4` | `permission:application.create` | None | Blade: `createstep4` |
| `GET` | `/step5` | `step5` | `CustomerController@step5` | `permission:application.create` | None | Blade: `createstep5` |
| `POST` | `/step5/upload`| `step5.upload`| `CustomerController@uploadDocument` | `permission:application.create` | `folder_id`, `file` (multipart or chunk token) | JSON `{status: 1, document_id}` |
| `POST` | `/step5/remove`| `step5.remove`| `CustomerController@removeDocument` | `permission:application.create` | `document_id` | JSON `{status: 1}` |
| `POST` | `/step5/requirement`| `step5.requirement`| `CustomerController@saveDocumentRequirement`| `permission:application.create`| `folder_id`, `is_required` | JSON `{status: 1}` |
| `GET` | `/step6` | `step6` | `CustomerController@step6` | `permission:application.create` | None | Blade: `createstep6` |
| `POST` | `/step6` | `step6.save` | `CustomerController@saveStep6` | `permission:application.create` | `challan_no`, `challan_date`, `bank_name`, `branch_name`, `amount` | Redirect -> `/step7` |
| `GET` | `/step7` | `step7` | `CustomerController@step7` | `permission:application.create` | None | Blade: `createstep7` |
| `POST` | `/step7` | `step7.save` | `CustomerController@saveStep7` | `permission:application.create` | `taluk`, `village`, `survey_numbers[]`, `extent_ha`, `extent_are`, `extent_sqm` | Redirect -> `/step8` |
| `GET` | `/step8` | `step8` | `CustomerController@step8` | `permission:application.create` | None | Blade: `createstep8` |
| `POST` | `/application/submit` | `application.submit` | `CustomerController@submit` | `permission:application.create` | `terms_accepted: 1` | Redirect -> `/application` |
| `POST` | `/application/{id}/approve` | `application.approve` | `CustomerController@approveApplication`| `permission:application.edit` | URL `{id}` | JSON `{status: 1}` |
| `POST` | `/application/{id}/move-to-mining`| `application.moveToMining`| `CustomerController@moveToMining`| `permission:application.edit`| URL `{id}` | JSON `{status: 1}` |

---

## 5. Mining, Environment & Surveys

| Module | Route / URI | Method | Action | Permission Gate | Primary Action |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Mining** | `/miningplan` | `GET` | `MiningController@index` | `mining.view` | Directory of mining plans |
| **Mining** | `/newapplication` | `GET` & `POST` | `MiningController@newApplication` / `store` | `mining.create` | Create 5-year mining scheme |
| **Mining** | `/mining/application/{id}/stage` | `POST` | `MiningController@advanceStage` | `mining.edit` | Transition: Scrutiny ➔ Inspection ➔ Presentation ➔ Approved |
| **Mining** | `/mining/application/{id}/move-to-environment` | `POST` | `MiningController@moveToEnvironment` | `mining.edit` | Handover to Environmental Clearance |
| **Environment** | `/eviron` | `GET` & `POST` | `EnverionsoneController@index` / `store` | `environment.view` / `create` | Unified B1/B2 project directory & create |
| **Environment** | `/eviron/{id}/submit-sc1-ppt` | `POST` | `EnverionsoneController@submitSc1ToPpt`| `environment.b2.review` | Submit SC1 appraisal to PPT |
| **DGPS** | `/dgps-survey` | `GET` & `POST` | `DgpsSurveyController@index` / `store` | `dgps.view` / `create` | DGPS demarcation & GPS coordinate points |
| **Drone** | `/drone-survey` | `GET` & `POST` | `DroneSurveyController@index` / `store` | `drone.view` / `create` | Drone flight & GeoTIFF deliverables |
| **PPT** | `/ppt-department` | `GET` & `POST` | `PptDepartmentController@index` / `store` | `ppt.view` / `create` | Presentation agenda & slide review |
| **EC Cert** | `/ec-certificate` | `GET` & `POST` | `EcCertificateController@index` / `store` | `ec_certificate.view` / `create` | Official EC order issuance |
| **EC Compl**| `/ec-compliance` | `GET` & `POST` | `EcComplianceController@index` / `store` | `ec_compliance.view` / `create` | Half-yearly monitoring compliance reports |

---

## 6. Accounts & Master Application Pipeline

| Module | Route / URI | Method | Controller Action | Permission Gate | Purpose |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Accounts** | `/accounts/quotations` | `GET`, `POST` | `QuotationController@index` / `store` | `account.view` / `create` | Multi-line statutory quotations |
| **Accounts** | `/accounts/quotations/{id}/print`| `GET` | `QuotationController@print` | `account.view` | Printable quote view |
| **Accounts** | `/accounts/payments` | `POST` | `PaymentCollectionController@store` | `account.create` | Collect customer remittance |
| **Accounts** | `/accounts/receipts` | `GET` | `PaymentReceiptController@index` | `account.view` | Official money receipts |
| **Accounts** | `/accounts/receipts/{id}/print` | `GET` | `PaymentReceiptController@print` | `account.view` | High-fidelity voucher print |
| **Accounts** | `/accounts/ledger/{customer}` | `GET` | `CustomerLedgerController@show` | `account.view` | Running debit/credit statement |
| **Accounts** | `/accounts/reports` | `GET` | `FinancialReportController@index` | `account.view` | Financial overview |
| **Accounts** | `/accounts/reports/export-csv` | `GET` | `FinancialReportController@exportCsv` | `account.view` | CSV ledger reconciliation export |
| **Accounts** | `/accounts/invoices/seal-upload`| `POST` | `InvoiceSealController@upload` | `account.edit` | Upload digital seal/signature PNG |
| **Pipeline** | `/master-pipeline` | `GET` | `MasterPipelineController@index` | Composite view perms | Statewide project pipeline hub |
| **Pipeline** | `/master-pipeline/timeline/{id}` | `GET` | `MasterPipelineController@timeline` | Composite view perms | Departmental timeline tracker |
| **Pipeline** | `/master-pipeline/forward/{id}` | `POST` | `MasterPipelineController@forward` | Composite edit perms | Advance project to downstream department |
| **Pipeline** | `/master-pipeline/return/{id}` | `POST` | `MasterPipelineController@returnForCorrection` | Composite edit perms | Send back to upstream department with remarks |
| **Chunk API** | `/api/upload/chunk/init` | `POST` | `ChunkUploadController@init` | `auth` | Initialize upload session & tokens |
| **Chunk API** | `/api/upload/chunk/upload` | `POST` | `ChunkUploadController@uploadChunk` | `auth` | Receive 2MB chunk slice |
| **Chunk API** | `/api/upload/chunk/assemble` | `POST` | `ChunkUploadController@assemble` | `auth` | Rebuild file and verify checksum |

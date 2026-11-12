# 09 — Routes & API Documentation

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Controllers: [05-controller-analysis.md](05-controller-analysis.md). Views: [10-blade-view-documentation.md](10-blade-view-documentation.md).
> Source: `routes/web.php` (642 lines, read in full). The table in §4 was generated mechanically from that file by parsing each `Route::` statement and its enclosing `middleware` / `prefix` groups, then spot-checked by hand. VERIFIED.
> `routes/api.php` does not exist. Every endpoint, including the JSON ones and `/api/upload/chunk/*`, is registered in `web.php`, so all of them run the **web** middleware group (session, cookies, **CSRF**). Laravel also registers `GET /up` (health) from `bootstrap/app.php`.

## 1. Totals
218 route declarations: 85 GET, 123 POST, 1 PUT, 4 DELETE, 1 GET|POST (`match`), 4 `resource(...)->only('index')`. Plus `/up`. Parameters are mostly path IDs (`{id}`, `{document}`, `{customer}`, `{quotation}`, `{receipt}`, `{slug}`, `{step}` with `whereNumber`) or query strings (`?id=` on `/viewapplication`, `/projectfolder`, `/document`, `/process`).

## 2. Middleware model
* `guest` — login routes. `throttle:15,1` on `POST /login`.
* `auth` — wraps routes/web.php lines 57–630.
* `permission:<name>` (Spatie) — per group or route. `a|b` means **any** of the permissions. Admin / Super Admin pass every check through `Gate::before`.
* Accounts controllers also declare permissions in `HasMiddleware::middleware()` (duplicating the route middleware).
* **No permission** (auth only): `/logout`, `/notifications/*`, `/accounts` (redirect), `/accounts/invoices/seal-*`, `/master-pipeline/*`.
* **No auth at all:** `/system/version`, `/api/upload/chunk/*`, `/up`.

## 3. Route problems found
| # | Problem | Evidence | Severity |
|---|---|---|---|
| R1 | Chunk upload API is unauthenticated | web.php:633-640 outside the `auth` group | Critical (see [18](18-security-audit.md) S1) |
| R2 | `DELETE /notifications/{id}` is declared before `DELETE /notifications/clear-all`, so `clear-all` is matched as `{id}='clear-all'` → `firstOrFail` 404. The "Clear all" action cannot work through that URL. | web.php:66-67 | Medium (functional bug, INFERRED from Laravel's first-match routing) |
| R3 | State-changing GET: `GET /master-pipeline/create-from-lease/{leaseId}` creates a master project | web.php:624 (`Route::match(['get','post'])`) | Medium (CSRF-able by link) |
| R4 | Master pipeline and invoice-seal endpoints have no permission check | web.php:612-628 | High |
| R5 | Duplicate alias routes for the same action (hyphen / underscore / legacy variants), e.g. mining upload ×3 and remove ×3, EC certificate ×3+×3, PPT ×3+×3, environment B1 ×4+×4, B2 ×3+×4, DGPS remove ×2, drone remove ×2, EC compliance ×2+×3 | web.php:323-330, 374-390, 420-425, 442-447, 465-467, 481-483, 498-502 | Low (maintenance; every alias must be secured identically) |
| R6 | Three GET routes all serve `MiningController@Document` (`/document`, `/mining/documents/{id?}`, `/mining-portal/documents/{id?}`) | web.php:312-314 | Low |
| R7 | Legacy redirects kept: `/environstage1`, `/environstage2`; legacy B2 routes `/environment-b2/*` remain alongside `/eviron/*` | web.php:351-361 | Low |
| R8 | `POST /notifications/seed-demo` (demo data generator) is reachable in production by any authenticated user; it deletes that user's notifications | web.php:68 | Low |
| R9 | `/system/version` is public and discloses PHP and Laravel versions | web.php:35-46 | Low |
| R10 | `GET /eviron/{id}` is declared before `/eviron/create`. Safe only because of `whereNumber('id')` | web.php:344-347 | Informational |
| R11 | `/customers/{slug}` and `/customers/lookup-mimas/{mimas_no}` coexist. Safe because slug is one segment, but `mimas_no` uses `->where('.*')` | web.php:215, 221 | Informational |
| R12 | No route uses `Route::resource` beyond `index`; create/update/delete use custom verb-named POST URLs (`useradd`, `branchedit`, `customerdelete`) | — | Informational |

## 4. Full route table
Columns: source line · verb · URI · name · middleware · action. "closure" = inline closure (`/system/version`, `/dashboard`, `/accounts` redirect).

| Line | Verb | URI | Name | Middleware | Action |
|---|---|---|---|---|---|
| 35 | GET | `/system/version` | system.version | web only | closure |
| 50 | GET | `/` | login | guest | AuthController@showLogin |
| 51 | GET | `/login` | � | guest | AuthController@showLogin |
| 52 | POST | `/login` | login.post | guest, throttle:15,1 | AuthController@login |
| 59 | POST | `/logout` | logout | auth | AuthController@logout |
| 62 | GET | `/notifications` | notifications.index | auth | NotificationController@index |
| 63 | POST | `/notifications/{id}/mark-as-read` | notifications.mark-as-read | auth | NotificationController@markAsRead |
| 64 | POST | `/notifications/{id}/mark-as-unread` | notifications.mark-as-unread | auth | NotificationController@markAsUnread |
| 65 | POST | `/notifications/mark-all-read` | notifications.mark-all-read | auth | NotificationController@markAllAsRead |
| 66 | DELETE | `/notifications/{id}` | notifications.destroy | auth | NotificationController@destroy |
| 67 | DELETE | `/notifications/clear-all` | notifications.clear-all | auth | NotificationController@clearAll |
| 68 | POST | `/notifications/seed-demo` | notifications.seed-demo | auth | NotificationController@seedDemo |
| 70 | GET | `/dashboard` | dashboard | auth, permission:dashboard.view | closure |
| 214 | GET | `/customers` | customers.index | auth, permission:customer.view | CustomerDirectoryController@index |
| 215 | GET | `/customers/{slug}` | customers.show | auth, permission:customer.view | CustomerDirectoryController@show |
| 216 | GET | `/customer-tracking` | customer-tracking.index | auth, permission:customer.view | CustomerTrackingController@index |
| 217 | GET | `/customer-tracking/search` | customer-tracking.search | auth, permission:customer.view | CustomerTrackingController@search |
| 218 | GET | `/customer-tracking/{customer}` | customer-tracking.show | auth, permission:customer.view | CustomerTrackingController@show |
| 219 | GET | `/customer-tracking/{customer}/proforma-invoice` | customer-tracking.proforma-invoice | auth, permission:customer.view | CustomerTrackingController@proformaInvoice |
| 220 | GET | `/customer-tracking/{customer}/tax-invoice` | customer-tracking.tax-invoice | auth, permission:customer.view | CustomerTrackingController@taxInvoice |
| 221 | GET | `/customers/lookup-mimas/{mimas_no}` | customers.lookup.mimas | auth, permission:customer.view | CustomerDirectoryController@lookupByMimas |
| 223 | POST | `/customeradd` | customeradd | auth, permission:customer.create | CustomerDirectoryController@store |
| 224 | POST | `/customeredit` | customeredit | auth, permission:customer.edit | CustomerDirectoryController@update |
| 225 | POST | `/customerdelete` | customerdelete | auth, permission:customer.delete | CustomerDirectoryController@destroy |
| 230 | GET | `/roles` | roles.index | auth, permission:roles.view | RolesController@index |
| 231 | GET | `/roles/{id}/permissions` | roles.permissions | auth, permission:roles.view | RolesController@getPermissions |
| 233 | POST | `/roleadd` | roleadd | auth, permission:roles.create | RolesController@store |
| 234 | POST | `/roleupdate` | roleupdate | auth, permission:roles.edit | RolesController@update |
| 235 | POST | `/roledelete` | roledelete | auth, permission:roles.delete | RolesController@destroy |
| 239 | GET | `/user` | user.index | auth, permission:users.view | UserController@index |
| 241 | POST | `/useradd` | useradd | auth, permission:users.create | UserController@store |
| 242 | POST | `/useredit` | useredit | auth, permission:users.edit | UserController@update |
| 243 | POST | `/userdelete` | userdelete | auth, permission:users.delete | UserController@destroy |
| 247 | GET | `/branch` | branch.index | auth, permission:branch.view | BranchController@index |
| 249 | POST | `/branchadd` | branchadd | auth, permission:branch.create | BranchController@store |
| 250 | POST | `/branchedit` | branchedit | auth, permission:branch.edit | BranchController@update |
| 251 | POST | `/branchdelete` | branchdelete | auth, permission:branch.delete | BranchController@destroy |
| 254 | GET | `/category` | category.index | auth, permission:category.view | CategoryController@index |
| 255 | POST | `/categoryadd` | categoryadd | auth, permission:category.create | CategoryController@store |
| 256 | POST | `/categoryedit` | categoryedit | auth, permission:category.edit | CategoryController@update |
| 257 | POST | `/categorydelete` | categorydelete | auth, permission:category.delete | CategoryController@destroy |
| 260 | GET | `/unit` | unit.index | auth, permission:unit.view | UnitController@index |
| 263 | GET | `/product` | product.index | auth, permission:product.view | ProductController@index |
| 264 | POST | `/productadd` | productadd | auth, permission:product.create | ProductController@store |
| 267 | GET | `/productstock` | productstock.index | auth, permission:product.view | ProductStockController@index |
| 271 | GET | `/application` | application.index | auth, permission:application.view | CustomerController@index |
| 272 | GET | `/viewapplication` | viewapplication | auth, permission:application.view | CustomerController@viewApplication |
| 275 | GET | `/step1` | step1 | auth, permission:application.create | CustomerController@step1 |
| 276 | POST | `/step1` | step1.save | auth, permission:application.create | CustomerController@saveStep1 |
| 277 | GET | `/step2` | step2 | auth, permission:application.create | CustomerController@step2 |
| 278 | POST | `/step2` | step2.save | auth, permission:application.create | CustomerController@saveStep2 |
| 279 | GET | `/step3` | step3 | auth, permission:application.create | CustomerController@step3 |
| 280 | POST | `/step3` | step3.save | auth, permission:application.create | CustomerController@saveStep3 |
| 281 | GET | `/step4` | step4 | auth, permission:application.create | CustomerController@step4 |
| 282 | GET | `/step5` | step5 | auth, permission:application.create | CustomerController@step5 |
| 283 | POST | `/step5/upload` | step5.upload | auth, permission:application.create | CustomerController@uploadDocument |
| 284 | POST | `/step5/remove` | step5.remove | auth, permission:application.create | CustomerController@removeDocument |
| 285 | POST | `/step5/requirement` | step5.requirement | auth, permission:application.create | CustomerController@saveDocumentRequirement |
| 286 | GET | `/step6` | step6 | auth, permission:application.create | CustomerController@step6 |
| 287 | POST | `/step6` | step6.save | auth, permission:application.create | CustomerController@saveStep6 |
| 288 | GET | `/step7` | step7 | auth, permission:application.create | CustomerController@step7 |
| 289 | POST | `/step7` | step7.save | auth, permission:application.create | CustomerController@saveStep7 |
| 290 | GET | `/step8` | step8 | auth, permission:application.create | CustomerController@step8 |
| 291 | POST | `/application/submit` | application.submit | auth, permission:application.create | CustomerController@submit |
| 292 | GET | `/application/{id}/resume` | application.resume | auth, permission:application.create | CustomerController@resumeDraft |
| 297 | POST | `/application/{id}/validate` | application.validate | auth, permission:application.edit | CustomerController@validateApplication |
| 298 | POST | `/application/{id}/approve` | application.approve | auth, permission:application.edit | CustomerController@approveApplication |
| 299 | POST | `/application/{id}/reject` | application.reject | auth, permission:application.edit | CustomerController@rejectApplication |
| 300 | POST | `/application/{id}/move-to-mining` | application.moveToMining | auth, permission:application.edit | CustomerController@moveToMining |
| 301 | POST | `/application/document/{id}/status` | application.document.status | auth, permission:application.edit | CustomerController@updateDocumentStatus |
| 302 | POST | `/application/document/{id}/upload` | application.document.upload | auth, permission:application.edit | CustomerController@uploadDossierDocument |
| 305 | GET | `/application/{id}/report` | application.report | auth, permission:application.view | CustomerController@generateReport |
| 310 | GET | `/miningplan` | miningplan.index | auth, permission:mining.view | MiningController@index |
| 311 | GET | `/projectfolder` | projectfolder | auth, permission:mining.view | MiningController@projectFolder |
| 312 | GET | `/document` | document | auth, permission:mining.view | MiningController@Document |
| 313 | GET | `/mining/documents/{id?}` | mining.documents | auth, permission:mining.view | MiningController@Document |
| 314 | GET | `/mining-portal/documents/{id?}` | mining-portal.documents | auth, permission:mining.view | MiningController@Document |
| 315 | GET | `/process` | process | auth, permission:mining.view | MiningController@Process |
| 318 | GET | `/newapplication` | newapplication | auth, permission:mining.create | MiningController@newApplication |
| 319 | POST | `/newapplication` | newapplication.store | auth, permission:mining.create | MiningController@store |
| 320 | POST | `/newapplication/autosave` | newapplication.autosave | auth, permission:mining.create | MiningController@autoSaveStep |
| 323 | POST | `/mining/document/upload` | mining.document.upload | auth, permission:mining.create\|mining.edit | MiningController@uploadDocument |
| 324 | POST | `/mining/document/remove` | mining.document.remove | auth, permission:mining.create\|mining.edit | MiningController@removeDocument |
| 325 | POST | `/mining-portal/upload-document` | mining-portal.upload-document | auth, permission:mining.create\|mining.edit | MiningController@uploadDocument |
| 326 | POST | `/mining-portal/upload` | mining-portal.upload | auth, permission:mining.create\|mining.edit | MiningController@uploadDocument |
| 327 | POST | `/mining_portal/upload-document` | mining_portal.upload_document | auth, permission:mining.create\|mining.edit | MiningController@uploadDocument |
| 328 | POST | `/mining-portal/remove-document` | mining-portal.remove-document | auth, permission:mining.create\|mining.edit | MiningController@removeDocument |
| 329 | POST | `/mining-portal/remove` | mining-portal.remove | auth, permission:mining.create\|mining.edit | MiningController@removeDocument |
| 330 | POST | `/mining_portal/remove-document` | mining_portal.remove_document | auth, permission:mining.create\|mining.edit | MiningController@removeDocument |
| 333 | POST | `/mining/document/{id}/validate` | mining.document.validate | auth, permission:mining.edit | MiningController@validateDocument |
| 334 | POST | `/mining/application/{id}/stage` | mining.application.stage | auth, permission:mining.edit | MiningController@advanceStage |
| 335 | POST | `/mining/application/{id}/move-to-environment` | mining.application.moveToEnvironment | auth, permission:mining.edit | MiningController@moveToEnvironment |
| 341 | GET | `/eviron` | eviron.index | auth, permission:environment.view | EnverionsoneController@index |
| 344 | GET | `/eviron/{id}` | eviron.show | auth, permission:environment.view | EnverionsoneController@show |
| 347 | GET | `/eviron/create` | eviron.create | auth, permission:environment.view | EnverionsoneController@create |
| 348 | GET | `/eviron/api/customer-lookup/{mimas_no}` | eviron.customer-lookup | auth, permission:environment.view | CustomerDirectoryController@lookupByMimas |
| 351 | GET | `/environstage1` | environstage1 | auth, permission:environment.view | EnverionsoneController@index1 |
| 352 | GET | `/environstage2` | environstage2 | auth, permission:environment.view | EnverionsoneController@index2 |
| 355 | GET | `/environment-b2` | environment-b2.index | auth, permission:environment.view | closure |
| 356 | GET | `/environment-b2/step/{step}` | environment-b2.step | auth, permission:environment.view | closure |
| 357 | GET | `/environment-b2/{project}` | environment-b2.show | auth, permission:environment.view | closure |
| 360 | GET | `/environment-b2/documents/{document}/download` | environment-b2.documents.download | auth, permission:environment.view | closure |
| 361 | GET | `/eviron/documents/{document}/download` | eviron.documents.download | auth, permission:environment.view | EnverionsoneController@downloadDocument |
| 366 | POST | `/eviron` | eviron.store | auth, permission:environment.b2.create | EnverionsoneController@store |
| 369 | POST | `/environment-b2` | environment-b2.store | auth, permission:environment.b2.create | closure |
| 374 | POST | `/eviron/{id}/documents/{document}/upload` | eviron.documents.upload | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | EnverionsoneController@uploadDocument |
| 375 | POST | `/eviron/{id}/documents/add` | eviron.documents.add | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | EnverionsoneController@addDocument |
| 376 | POST | `/eviron/{id}/documents/{document}/remove` | eviron.documents.remove | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | EnverionsoneController@removeDocument |
| 377 | POST | `/eviron/documents/remove` | eviron.documents.remove_direct | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | EnverionsoneController@removeDocument |
| 378 | POST | `/enviro_b1/upload-document` | enviro_b1.upload_document | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | EnverionsoneController@uploadDocument |
| 379 | POST | `/enviro-b1/upload` | enviro-b1.upload | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | EnverionsoneController@uploadDocument |
| 380 | POST | `/enviro_b1/remove-document` | enviro_b1.remove_document | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | EnverionsoneController@removeDocument |
| 381 | POST | `/enviro-b1/remove` | enviro-b1.remove | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | EnverionsoneController@removeDocument |
| 384 | POST | `/environment-b2/documents/{document}/upload` | environment-b2.documents.upload | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | closure |
| 385 | POST | `/environment-b2/documents/{document}/remove` | environment-b2.documents.remove | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | closure |
| 386 | POST | `/environment-b2/documents/remove` | environment-b2.documents.remove_direct | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | closure |
| 387 | POST | `/enviro_b2/upload-document` | enviro_b2.upload_document | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | closure |
| 388 | POST | `/enviro-b2/upload` | enviro-b2.upload | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | closure |
| 389 | POST | `/enviro_b2/remove-document` | enviro_b2.remove_document | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | closure |
| 390 | POST | `/enviro-b2/remove` | enviro-b2.remove | auth, permission:environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create | closure |
| 395 | POST | `/eviron/{id}/status` | eviron.status | auth, permission:environment.b2.review | EnverionsoneController@updateStatus |
| 396 | POST | `/eviron/{id}/submit-sc1-ppt` | eviron.submitSc1ToPpt | auth, permission:environment.b2.review | EnverionsoneController@submitSc1ToPpt |
| 397 | POST | `/eviron/{id}/submit-sc2-ppt` | eviron.submitSc2ToPpt | auth, permission:environment.b2.review | EnverionsoneController@submitSc2ToPpt |
| 398 | POST | `/eviron/{id}/submit-b2-ppt` | eviron.submitB2ToPpt | auth, permission:environment.b2.review | EnverionsoneController@submitB2ToPpt |
| 401 | POST | `/eviron/{id}/documents/{document}/review` | eviron.documents.review | auth, permission:environment.b2.review | EnverionsoneController@reviewDocument |
| 404 | POST | `/environment-b2/{project}/status` | environment-b2.status | auth, permission:environment.b2.review | closure |
| 405 | POST | `/environment-b2/documents/{document}/review` | environment-b2.documents.review | auth, permission:environment.b2.review | closure |
| 411 | GET | `/ec-certificate` | ec-certificate.index | auth, permission:ec_certificate.view | EcCertificateController@index |
| 412 | GET | `/ec-certificate/{id}` | ec-certificate.show | auth, permission:ec_certificate.view | EcCertificateController@show |
| 415 | GET | `/ec-certificate/step/{step}` | ec-certificate.step | auth, permission:ec_certificate.create | EcCertificateController@wizard |
| 416 | POST | `/ec-certificate/step/{step}` | ec-certificate.saveStep | auth, permission:ec_certificate.create | EcCertificateController@saveStep |
| 417 | POST | `/ec-certificate` | ec-certificate.store | auth, permission:ec_certificate.create | EcCertificateController@store |
| 420 | POST | `/ec-certificate/upload` | ec-certificate.upload | auth, permission:ec_certificate.create\|ec_certificate.edit | EcCertificateController@uploadDocument |
| 421 | POST | `/ec-certificate/upload-document` | ec-certificate.upload-document | auth, permission:ec_certificate.create\|ec_certificate.edit | EcCertificateController@uploadDocument |
| 422 | POST | `/ec_certificate/upload-document` | ec_certificate.upload_document | auth, permission:ec_certificate.create\|ec_certificate.edit | EcCertificateController@uploadDocument |
| 423 | POST | `/ec-certificate/remove` | ec-certificate.remove | auth, permission:ec_certificate.create\|ec_certificate.edit | EcCertificateController@removeDocument |
| 424 | POST | `/ec-certificate/remove-document` | ec-certificate.remove-document | auth, permission:ec_certificate.create\|ec_certificate.edit | EcCertificateController@removeDocument |
| 425 | POST | `/ec_certificate/remove-document` | ec_certificate.remove_document | auth, permission:ec_certificate.create\|ec_certificate.edit | EcCertificateController@removeDocument |
| 428 | POST | `/ec-certificate/{id}/status` | ec-certificate.status | auth, permission:ec_certificate.edit | EcCertificateController@updateStatus |
| 433 | GET | `/ppt-department` | ppt-department.index | auth, permission:ppt.view | PptDepartmentController@index |
| 434 | GET | `/ppt-department/{id}` | ppt-department.show | auth, permission:ppt.view | PptDepartmentController@show |
| 437 | GET | `/ppt-department/step/{step}` | ppt-department.step | auth, permission:ppt.create | PptDepartmentController@wizard |
| 438 | POST | `/ppt-department/step/{step}` | ppt-department.saveStep | auth, permission:ppt.create | PptDepartmentController@saveStep |
| 439 | POST | `/ppt-department` | ppt-department.store | auth, permission:ppt.create | PptDepartmentController@store |
| 442 | POST | `/ppt-department/upload` | ppt-department.upload | auth, permission:ppt.create\|ppt.edit | PptDepartmentController@uploadDocument |
| 443 | POST | `/ppt_department/upload-document` | ppt_department.upload_document | auth, permission:ppt.create\|ppt.edit | PptDepartmentController@uploadDocument |
| 444 | POST | `/ppt-department/upload-document` | ppt-department.upload_document | auth, permission:ppt.create\|ppt.edit | PptDepartmentController@uploadDocument |
| 445 | POST | `/ppt-department/remove` | ppt-department.remove | auth, permission:ppt.create\|ppt.edit | PptDepartmentController@removeDocument |
| 446 | POST | `/ppt_department/remove-document` | ppt_department.remove_document | auth, permission:ppt.create\|ppt.edit | PptDepartmentController@removeDocument |
| 447 | POST | `/ppt-department/remove-document` | ppt-department.remove_document | auth, permission:ppt.create\|ppt.edit | PptDepartmentController@removeDocument |
| 450 | POST | `/ppt-department/{id}/status` | ppt-department.status | auth, permission:ppt.edit | PptDepartmentController@updateStatus |
| 451 | POST | `/ppt-department/{id}/approve-stage` | ppt-department.approveStage | auth, permission:ppt.edit | PptDepartmentController@approvePresentation |
| 456 | GET | `/dgps-survey` | dgps-survey.index | auth, permission:dgps.view | DgpsSurveyController@index |
| 457 | GET | `/dgps-survey/{id}` | dgps-survey.show | auth, permission:dgps.view | DgpsSurveyController@show |
| 460 | GET | `/dgps-survey/step/{step}` | dgps-survey.step | auth, permission:dgps.create | DgpsSurveyController@wizard |
| 461 | POST | `/dgps-survey/step/{step}` | dgps-survey.saveStep | auth, permission:dgps.create | DgpsSurveyController@saveStep |
| 462 | POST | `/dgps-survey` | dgps-survey.store | auth, permission:dgps.create | DgpsSurveyController@store |
| 465 | POST | `/dgps-survey/upload` | dgps-survey.upload | auth, permission:dgps.edit\|dgps.create | DgpsSurveyController@uploadDocument |
| 466 | POST | `/dgps-survey/remove` | dgps-survey.remove | auth, permission:dgps.edit\|dgps.create | DgpsSurveyController@removeDocument |
| 467 | POST | `/dgps_survey/remove-document` | dgps_survey.remove_document | auth, permission:dgps.edit\|dgps.create | DgpsSurveyController@removeDocument |
| 472 | GET | `/drone-survey` | drone-survey.index | auth, permission:drone.view | DroneSurveyController@index |
| 473 | GET | `/drone-survey/{id}` | drone-survey.show | auth, permission:drone.view | DroneSurveyController@show |
| 476 | GET | `/drone-survey/step/{step}` | drone-survey.step | auth, permission:drone.create | DroneSurveyController@wizard |
| 477 | POST | `/drone-survey/step/{step}` | drone-survey.saveStep | auth, permission:drone.create | DroneSurveyController@saveStep |
| 478 | POST | `/drone-survey` | drone-survey.store | auth, permission:drone.create | DroneSurveyController@store |
| 481 | POST | `/drone-survey/upload` | drone-survey.upload | auth, permission:drone.edit\|drone.create | DroneSurveyController@uploadDocument |
| 482 | POST | `/drone-survey/remove` | drone-survey.remove | auth, permission:drone.edit\|drone.create | DroneSurveyController@removeDocument |
| 483 | POST | `/drone_survey/remove-document` | drone_survey.remove_document | auth, permission:drone.edit\|drone.create | DroneSurveyController@removeDocument |
| 488 | GET | `/ec-compliance` | ec-compliance.index | auth, permission:ec_compliance.view | EcComplianceController@index |
| 489 | GET | `/ec-compliance/{id}` | ec-compliance.show | auth, permission:ec_compliance.view | EcComplianceController@show |
| 492 | GET | `/ec-compliance/step/{step}` | ec-compliance.step | auth, permission:ec_compliance.create | EcComplianceController@wizard |
| 493 | POST | `/ec-compliance/step/{step}` | ec-compliance.saveStep | auth, permission:ec_compliance.create | EcComplianceController@saveStep |
| 494 | POST | `/ec-compliance` | ec-compliance.store | auth, permission:ec_compliance.create | EcComplianceController@store |
| 497 | POST | `/ec-compliance/{id}/status` | ec-compliance.status | auth, permission:ec_compliance.edit\|ec_compliance.create | EcComplianceController@updateStatus |
| 498 | POST | `/ec-compliance/upload` | ec-compliance.upload | auth, permission:ec_compliance.edit\|ec_compliance.create | EcComplianceController@uploadDocument |
| 499 | POST | `/ec_compliance/upload-document` | ec_compliance.upload_document | auth, permission:ec_compliance.edit\|ec_compliance.create | EcComplianceController@uploadDocument |
| 500 | POST | `/ec-compliance/remove` | ec-compliance.remove | auth, permission:ec_compliance.edit\|ec_compliance.create | EcComplianceController@removeDocument |
| 501 | POST | `/ec-compliance/remove-document` | ec-compliance.remove_document | auth, permission:ec_compliance.edit\|ec_compliance.create | EcComplianceController@removeDocument |
| 502 | POST | `/ec_compliance/remove-document` | ec_compliance.remove_document | auth, permission:ec_compliance.edit\|ec_compliance.create | EcComplianceController@removeDocument |
| 507 | GET | `/accounts/` | index | auth | closure |
| 512 | GET | `/accounts/bank-accounts` | accounts.bank-accounts.index | auth, permission:account.view | QuotationController@getBankAccounts |
| 516 | POST | `/accounts/bank-accounts` | accounts.bank-accounts.store | auth, permission:account.create | QuotationController@storeBankAccount |
| 521 | GET | `/accounts/quotations/customer-concessions/{customer}` | accounts.quotations.customer-concessions | auth, permission:account.view | QuotationController@getCustomerConcessions |
| 526 | GET | `/accounts/quotations/{quotation}/print` | accounts.quotations.print | auth, permission:account.view | QuotationController@print |
| 531 | GET | `/accounts/quotations` | accounts.quotations.index | auth, permission:account.view | QuotationController@index |
| 535 | GET | `/accounts/quotations/create` | accounts.quotations.create | auth, permission:account.create | QuotationController@create |
| 539 | POST | `/accounts/quotations` | accounts.quotations.store | auth, permission:account.create | QuotationController@store |
| 543 | GET | `/accounts/quotations/{quotation}` | accounts.quotations.show | auth, permission:account.view | QuotationController@show |
| 547 | GET | `/accounts/quotations/{quotation}/edit` | accounts.quotations.edit | auth, permission:account.edit | QuotationController@edit |
| 551 | PUT | `/accounts/quotations/{quotation}` | accounts.quotations.update | auth, permission:account.edit | QuotationController@update |
| 555 | DELETE | `/accounts/quotations/{quotation}` | accounts.quotations.destroy | auth, permission:account.delete | QuotationController@destroy |
| 560 | GET | `/accounts/payments/create` | accounts.payments.create | auth, permission:account.create | PaymentCollectionController@create |
| 564 | POST | `/accounts/payments` | accounts.payments.store | auth, permission:account.create | PaymentCollectionController@store |
| 568 | GET | `/accounts/payments/customer-dues/{customer}` | accounts.payments.customer-dues | auth, permission:account.view | PaymentCollectionController@getCustomerPendingDues |
| 573 | GET | `/accounts/receipts` | accounts.receipts.index | auth, permission:account.view | PaymentReceiptController@index |
| 577 | GET | `/accounts/receipts/application-history/{type}/{id}` | accounts.receipts.application-history | auth, permission:account.view | PaymentReceiptController@getApplicationReceipts |
| 581 | GET | `/accounts/receipts/{receipt}` | accounts.receipts.show | auth, permission:account.view | PaymentReceiptController@show |
| 585 | GET | `/accounts/receipts/{receipt}/print` | accounts.receipts.print | auth, permission:account.view | PaymentReceiptController@print |
| 590 | GET | `/accounts/ledger` | accounts.ledger.index | auth, permission:account.view | CustomerLedgerController@index |
| 594 | GET | `/accounts/ledger/{customer}` | accounts.ledger.show | auth, permission:account.view | CustomerLedgerController@show |
| 598 | GET | `/accounts/ledger/{customer}/print` | accounts.ledger.print | auth, permission:account.view | CustomerLedgerController@printStatement |
| 603 | GET | `/accounts/reports` | accounts.reports.index | auth, permission:account.view | FinancialReportController@index |
| 607 | GET | `/accounts/reports/export-csv` | accounts.reports.export-csv | auth, permission:account.view | FinancialReportController@exportCsv |
| 612 | POST | `/accounts/invoices/seal-upload` | accounts.invoices.seal.upload | auth | InvoiceSealController@upload |
| 614 | POST | `/accounts/invoices/seal-remove` | accounts.invoices.seal.remove | auth | InvoiceSealController@remove |
| 616 | GET | `/accounts/invoices/seal-status` | accounts.invoices.seal.status | auth | InvoiceSealController@status |
| 622 | GET | `/master-pipeline/` | master-pipeline.index | auth | MasterPipelineController@index |
| 623 | GET | `/master-pipeline/timeline/{id}` | master-pipeline.timeline | auth | MasterPipelineController@timeline |
| 624 | GET\|POST | `/master-pipeline/create-from-lease/{leaseId}` | master-pipeline.create-from-lease | auth | MasterPipelineController@createFromLease |
| 625 | POST | `/master-pipeline/forward/{id}` | master-pipeline.forward | auth | MasterPipelineController@forward |
| 626 | POST | `/master-pipeline/return/{id}` | master-pipeline.return | auth | MasterPipelineController@returnForCorrection |
| 627 | POST | `/master-pipeline/resubmit/{id}` | master-pipeline.resubmit | auth | MasterPipelineController@resubmit |
| 634 | POST | `/api/upload/chunk/init` | api.upload.chunk.init | web only | ChunkUploadController@init |
| 635 | POST | `/api/upload/chunk/upload` | api.upload.chunk.upload | web only | ChunkUploadController@uploadChunk |
| 636 | GET | `/api/upload/chunk/status` | api.upload.chunk.status | web only | ChunkUploadController@status |
| 637 | POST | `/api/upload/chunk/assemble` | api.upload.chunk.assemble | web only | ChunkUploadController@assemble |
| 638 | POST | `/api/upload/chunk/cancel` | api.upload.chunk.cancel | web only | ChunkUploadController@cancel |
| 639 | DELETE | `/api/upload/chunk/cancel` | api.upload.chunk.cancel.delete | web only | ChunkUploadController@cancel |

## 5. End-to-end traces (VERIFIED for the paths shown)

**T1 — Submit lease application**
User clicks "Submit" on step 8 → `createstep8.blade.php` form POST → `/application/submit` → `web` (CSRF) → `auth` → `permission:application.create` → `CustomerController@submit` → reads session `lease_draft` → DB transaction (customers, lease_applications renumbered `LA-YYYY-NNNN` with status `under_scrutiny`, mimas_credentials, application_payments, application_handlers, lease_documents ×19, activity_logs) → copies files to `public/uploads/lease_applications/<no>/` → `session()->forget('lease_draft')` → redirect `/application` with a flash message (or JSON).

**T2 — Upload a large document (chunked)**
`gtms-chunk-uploader.js` → POST `/api/upload/chunk/init` (no auth) → POST `/api/upload/chunk/upload` ×N (with `X-CSRF-TOKEN`) → POST `/api/upload/chunk/assemble` or the module endpoint (e.g. `/step5/upload` with `upload_token`) → `ChunkedUploadService::assembleFile` → `public/uploads/<module>/REF-<id>/<ts>_<rand>_<slug>.<ext>` → `ProcessUploadedMediaJob` dispatched to Redis → the queue worker runs `VerifyFileChecksumJob` and, for video, `GenerateVideoThumbnailJob`.

**T3 — Collect a payment**
`accounts/payments/create.blade.php` → AJAX GET `/accounts/payments/customer-dues/{customer}` → form POST `/accounts/payments` → `permission:account.create` → `PaymentCollectionController@store` → `lockForUpdate` on the application row → update paid / pending / status → `application_payments` upsert → `payment_receipts` create → redirect `/accounts/receipts/{id}`.

**T4 — Return a file for correction**
`master_pipeline/timeline.blade.php` modal form → POST `/master-pipeline/return/{id}` (auth only) → `MasterPipelineController@returnForCorrection` (validates reason ≥5 and priority) → `MasterPipelineService::returnForCorrection` → master `current_stage` = target, `pipeline_status` = `returned_for_correction`, both department records' `status` = `returned_for_correction` → `pipeline_movements` row → `notifications` rows → redirect to the timeline.

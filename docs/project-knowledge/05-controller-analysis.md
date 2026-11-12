# 05 — Controller Analysis

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Routes: [09-routes-api-documentation.md](09-routes-api-documentation.md). Modules: [04-module-documentation.md](04-module-documentation.md).
> Columns: **Method** · **Route** (method + URI; permission in brackets) · **Reads / writes** (tables) · **Validation** · **Response**. "auth" means only the `auth` middleware applies.
> Depth: **F** = body read in full; **K** = key logic read; **S** = signature + targeted grep only.

## Common patterns (VERIFIED)
* All controllers extend the empty `App\Http\Controllers\Controller`.
* Accounts controllers implement `HasMiddleware` and declare permission middleware in `middleware()`. All other controllers rely on route middleware.
* Most module controllers repeat the same blocks: `uploadDocument` (extension allow-list, max 500 MB, optional `upload_token`), `removeDocument` (accepts id from route **or** several request keys, unlinks under `public/`, `public/storage/` and `storage/app/public/`), `wizard` / `saveStep` (session draft), `store` (transaction → main record + `application_handlers` + `application_payments` + documents).
* Status updates usually also write `activity_logs` with `user_id = Auth::id() ?? 1`.

---

## AuthController — F
| Method | Route | Reads / writes | Validation | Response |
|---|---|---|---|---|
| showLogin | GET `/`, `/login` (guest) | — | — | view `pages.login` or redirect `/dashboard` |
| login | POST `/login` (guest, throttle 15,1) | users | email, password required strings | redirect intended `/dashboard` / back with errors |
| logout | POST `/logout` (auth) | sessions | — | redirect `/` |

## UserController — F
| index | GET `/user` [users.view] | users, roles, branches | — | view `pages.authentication.users.index` |
| store | POST `useradd` [users.create] | users, model_has_roles; file `public/uploads/users/` | name, unique email, unique user_code, password min 6, role_id, branch_id exist, mobile 10-15 digits, image ≤2 MB | JSON `{status,message,data:user}` — data includes `show_password` |
| update | POST `useredit` [users.edit] | same | same + status 0/1 | JSON |
| destroy | POST `userdelete` [users.delete] | users; checks customers, lease, mining, env, dgps, drone, ec_certificates by `created_by` | id exists | JSON |

## RolesController — F
| index | GET `/roles` [roles.view] | roles, permissions | — | view |
| getPermissions | GET `roles/{id}/permissions` [roles.view] | roles | — | JSON |
| store | POST `roleadd` [roles.create] | roles, role_has_permissions | unique name, permissions exist | JSON |
| update | POST `roleupdate` [roles.edit] | same; Admin / Super Admin always re-synced to all permissions | — | JSON |
| destroy | POST `roledelete` [roles.delete] | roles; blocked if users assigned | — | JSON |

## BranchController — F
| index | GET `/branch` [branch.view] | branches, users | — | view |
| store / update | POST `branchadd` / `branchedit` | branches | name, contact, mobile 10-15, address, pincode 6 | JSON |
| destroy | POST `branchdelete` [branch.delete] | **deletes all users with this branch_id** + branch (transaction) | id exists | JSON |

## CategoryController / ProductController / ProductStockController / UnitController — F
| Category index/store/update/destroy | `/category`, `categoryadd`, `categoryedit`, `categorydelete` | categories (soft flag `delete_status`) | unique code / name | view / JSON |
| Product index/store | `/product`, `productadd` | products, product_stocks | required fields only | view / JSON |
| ProductStock index | `/productstock` [product.view] | product_stocks | — | view |
| Unit index | `/unit` [unit.view] | units | — | view |

## CustomerDirectoryController — K
| index | GET `/customers` [customer.view] | customers, districts, minerals | — | view `pages.customers` |
| store | POST `/customeradd` [customer.create] | customers | see M5 rules | JSON |
| show | GET `/customers/{slug}` [customer.view] | customers + relations | — | view `pages.customer_show` |
| update | POST `/customeredit` [customer.edit] | customers | same as store (unique ignoring self) | JSON |
| lookupByMimas | GET `/customers/lookup-mimas/{mimas_no}` [customer.view]; GET `/eviron/api/customer-lookup/{mimas_no}` [environment.view] | customers | — | JSON incl. **PAN, Aadhaar, parivesh_password, email_password** |
| destroy | POST `/customerdelete` [customer.delete] | customers (soft) | blocks if dependants | JSON |

## CustomerTrackingController — K/S
| index | GET `/customer-tracking` [customer.view] | all module tables (counts) | filters | view `pages.customer_tracking.index` (4,314 lines) |
| search | GET `/customer-tracking/search` | customers + modules | `q` | JSON / view |
| show | GET `/customer-tracking/{customer}` | dossier | — | view |
| proformaInvoice / taxInvoice | GET `/customer-tracking/{customer}/proforma-invoice`, `/tax-invoice` | dossier → invoice data | optional selection | view (HTML print) |

## CustomerController (Lease Application) — K
| index | GET `/application` [application.view] | lease_applications (+relations, no pagination) | — | view `lease_application.customer` |
| viewApplication | GET `/viewapplication?id=` [application.view] | lease + docs + logs | — | view `viewapplication` |
| step1..step8 (GET) | `/step1`…`/step8` [application.create] | session `lease_draft`, lookups | — | views `createstep1..8` |
| saveStep1 | POST `/step1` | customers (find / restore / create), lease_applications (draft), lease_application_minerals | mimas_no, client_name, district, mobile, pan, aadhaar required; area regex | JSON `{status, redirect}` |
| saveStep2 | POST `/step2` | lease_applications, mimas_credentials | contact, MIMAS user / email required | JSON |
| saveStep3 | POST `/step3` | lease_categories (may create custom), lease_applications | category_id or other rule | JSON |
| uploadDocument / removeDocument / saveDocumentRequirement | POST `/step5/upload`, `/step5/remove`, `/step5/requirement` | session + public/uploads, chunked_uploads | extension allow-list, ≤500 MB | JSON |
| saveStep6 / saveStep7 | POST `/step6`, `/step7` | session (payment, handlers) | — | JSON |
| resumeDraft | GET `/application/{id}/resume` | rebuilds the session from the DB | — | redirect to step |
| submit | POST `/application/submit` | lease_applications, customers, mimas_credentials, application_payments, application_handlers, lease_documents, activity_logs, files | — (relies on session) | redirect `/application` or JSON; optionally calls moveToMining |
| validateApplication | POST `/application/{id}/validate` [application.edit] | lease_applications, lease_documents, activity_logs | action pass / fail | back / JSON |
| updateDocumentStatus | POST `/application/document/{id}/status` [application.edit] | lease_documents | status in 5 values | JSON / back |
| uploadDossierDocument | POST `/application/document/{id}/upload` [application.edit] | lease_documents, files | allow-list | JSON |
| approveApplication | POST `/application/{id}/approve` [application.edit] | lease_applications | requires validated / under_scrutiny | back / JSON |
| rejectApplication | POST `/application/{id}/reject` [application.edit] | lease_applications | reason required | back / JSON |
| generateReport | GET `/application/{id}/report` [application.view] | lease + mining + quotation + logs | — | HTML `report_pdf` |
| moveToMining | POST `/application/{id}/move-to-mining` [application.edit] | mining_applications, mining_documents, activity_logs, files | requires approved | redirect / JSON |
| helpers | generateDraftAppNumber, generateOfficialAppNumber (lockForUpdate, but subject to BranchScope), parseAreaExtentToFloat (H.A.SqM → ha), logActivity | | | |

## MiningController — K
| index | GET `/miningplan` [mining.view] | mining_applications (no pagination) | — | view `mining-portal.index` |
| projectFolder | GET `/projectfolder?id=` [mining.view] | mining + folders + docs | — | view |
| Document | GET `/document`, `/mining/documents/{id?}`, `/mining-portal/documents/{id?}` | mining_documents | — | view `mining-portal.document` |
| Process | GET `/process` [mining.view] | mining | — | view `mining-portal.process` |
| newApplication | GET `/newapplication` [mining.create] | lookups | — | view (7,229 lines) |
| store | POST `/newapplication` [mining.create] | mining_applications, minerals pivot, application_payments, application_handlers, mining_documents | nature_of_work, applicant_type, district required; conditional plan fields | redirect `projectfolder` |
| autoSaveStep | POST `/newapplication/autosave` | mining_applications (draft) | current_step 1-9 | JSON |
| uploadDocument / removeDocument | 6 alias routes [mining.create\|mining.edit] | mining_documents, files | allow-list | JSON |
| validateDocument | POST `/mining/document/{id}/validate` [mining.edit] | mining_documents | — | back |
| advanceStage | POST `/mining/application/{id}/stage` [mining.edit] | mining_applications | **none** | back |
| moveToEnvironment | POST `/mining/application/{id}/move-to-environment` [mining.edit] | environment_projects, environment_documents, activity_logs | category default B2 | redirect `environment-b2.show` / JSON |

## EnverionsoneController (Environmental Clearance) — K
| index | GET `/eviron` [environment.view] | environment_projects | filters | view `eviron.index` |
| create | GET `/eviron/create` | lookups | — | view `eviron.create` |
| store | POST `/eviron` [environment.b2.create] | environment_projects, customers, application_handlers / payments, environment_documents, files (public disk) | category B1/B2, sub_category, client, project, district, phone | redirect show |
| show | GET `/eviron/{id}` | project + docs | — | view `eviron.show` (2,439 lines) |
| uploadDocument / addDocument / removeDocument | several routes [environment.b2.upload\|environment.create\|environment.edit\|environment.b2.create] | environment_documents, files | allow-list | JSON / back |
| reviewDocument | POST `/eviron/{id}/documents/{document}/review` [environment.b2.review] | environment_documents | approved / revision_required | back |
| updateStatus | POST `/eviron/{id}/status` [environment.b2.review] | environment_projects, environment_documents, activity_logs | status string ≤100 | back |
| downloadDocument | GET `/eviron/documents/{document}/download` | public disk | — | file download |
| index1 / index2 | GET `/environstage1`, `/environstage2` | — | — | redirects (legacy) |
| submitSc1ToPpt / submitSc2ToPpt / submitB2ToPpt | POST `/eviron/{id}/submit-*-ppt` [environment.b2.review] | ppt_applications, application_handlers, environment_projects, activity_logs | category guards (abort 400) | redirect / JSON |
| autoGenerateDocumentSlots / generateSlots | internal | folders (module 1), document_fields, environment_documents | — | — |

## EnvironmentalB2Controller (legacy B2) — S
`index`, `wizard(step)`, `store`, `show(EnvironmentProject)`, `upload`, `removeDocument`, `review`, `updateStatus` (blocks `validation` without uploaded docs and `approved` without approved docs), `download` (from `public_path`). Routes are under `/environment-b2/*` and the `enviro_b2` / `enviro-b2` aliases.

## PptDepartmentController — K
`index`, `show`, `wizard(1..9)`, `saveStep` (session `ppt_wizard`), `store` (PPT-YYYY-NNNN via `withoutGlobalScopes()->count()+1` + exists loop), upload / remove (3+3 aliases), `updateStatus` [ppt.edit] (free string; `approved` triggers the environment side effects), `approvePresentation` [ppt.edit] (see [12-business-logic.md](12-business-logic.md) R-ENV).

## EcCertificateController — K
`index`, `show`, `wizard` + `getMaxUnlockedStep`, `saveStep` (step validation incl. the certificate file), `store` (updateOrCreate by ec_ref_no; project → approved; handlers + payment), upload / remove (3+3 aliases), `updateStatus` [ec_certificate.edit] (free string).

## EcComplianceController — K
`index`, `wizard(1..8)`, `saveStep`, `store` (HYC-YYYY-NNNN, status `uploaded_to_parivesh`, placeholder numbers), `show`, upload / remove (2+3 aliases), `updateStatus` [ec_compliance.edit\|create] (free string).

## DgpsSurveyController / DroneSurveyController — K
`index` (filters by status), `wizard(1..6)`, `saveStep`, `store` (DGPS / DRN numbering; placeholder values; see M13 / M14), `show`, `uploadDocument`, `removeDocument`.

## Accounts\QuotationController — K
`index`, `create`, `store` (items, totals via `Quotation::calculateTotals`, number `GTMS/QTN/YYYY/NNNN`), `show`, `edit`, `update`, `destroy` (soft delete, items hard-deleted), `print`, `getCustomerConcessions` (JSON), `getBankAccounts`, `storeBankAccount`, `getDefaultScopeSections`, `getDefaultMilestones`, parsers.

## Accounts\PaymentCollectionController — K
`create` (desk view), `getCustomerPendingDues($customer)` (JSON across all modules), `store` (see M15).

## Accounts\PaymentReceiptController — S
`index` (filters, KPIs), `show`, `print`, `buildReceiptInvoiceData` (≈345 lines of print data), `getApplicationReceipts(type, id)` (JSON).

## Accounts\CustomerLedgerController — S
`index`, `show`, `printStatement`, `resolveCustomer` (id or slug), `getCustomerApplicationDues`.

## Accounts\FinancialReportController — S
`index`, `exportCsv` (StreamedResponse), `buildFilteredQuery`, `buildAnalyticsData`.

## Accounts\InvoiceSealController — F
`getCustomSealUrl` (static, used by print views), `upload` (image incl. **svg**, ≤5 MB), `remove`, `status`. Routes `/accounts/invoices/seal-*` — **auth only**.

## MasterPipelineController — F
`index` (KPIs, filters), `timeline`, `createFromLease` (GET\|POST), `forward`, `returnForCorrection`, `resubmit`. All routes are **auth only**. Delegates to `MasterPipelineService`.

## NotificationController — F
`index` (auto-seeds demo data), `markAsRead`, `markAsUnread`, `markAllAsRead`, `destroy`, `clearAll` (**shadowed by `DELETE /notifications/{id}`**), `seedDemo`, `seedDefaultNotifications`.

## ChunkUploadController — F
`init`, `uploadChunk`, `status`, `assemble`, `cancel`. **No auth.** Delegates to `ChunkedUploadService`. Error messages echo exception text to the client.

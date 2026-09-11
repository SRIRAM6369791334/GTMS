# 01 - UI Project Inventory

**GTMS (Granite / Mining Tracking Management System)** — District Mining Office Management System.

This document provides a complete inventory of every UI screen in the project, its file path, its purpose, and the main business entity/module it belongs to.

---

## Navigation Structure (from `layouts/sidebar.blade.php`)

| Module | Menu Item | Route | Permission Gate |
| ------ | --------- | ----- | --------------- |
| Dashboard | Home | `/dashboard` | `dashboard.view` |
| Authentication | Department | `/branch` | `branch.view` |
| Authentication | Role | `/roles` | `roles.view` |
| Authentication | Users | `/user` | `users.view` |
| Customers | Customers | `/customers` | (no gate) |
| Lease Applications | Application | `/application` | `application.view` |
| Mining Plan | Mining Plan | `/miningplan` | `mining.view` |
| Mining Plan | Project Folders | `/projectfolder` | `mining.view` |
| Mining Plan | Process | `/process` | `mining.view` |
| Environment Clearance | Sub Category 1 | `/environstage1` | `environment.view` |
| Environment Clearance | Sub Category 2 | `/environstage2` | `environment.view` |
| Environment Clearance | B2 Document Process | `/environment-b2` | `environment.view` |
| Environment Clearance | EC Certificate Issuance | `/ec-certificate` | `environment.view` |
| PPT Department | Online Domain Process | `/ppt-department` | `ppt.view` |
| DGPS Survey | DGPS Survey | `/dgps-survey` | `dgps.view` |
| Drone Survey | Drone Survey | `/drone-survey` | `drone.view` |

---

## Page / Screen Inventory

| # | Page / Screen | File Path | Purpose | Main Entity / Module |
|---| ------------- | --------- | ------- | -------------------- |
| 1 | Login | `resources/views/pages/login.blade.php` | Authenticate system users (email or user code + password) | User / Auth |
| 2 | Dashboard | `resources/views/pages/index.blade.php` | Regional KPI dashboard: active applications, pending actions across districts, stats cards, charts, recent activity | Dashboard (aggregated) |
| 3 | Layout — App | `resources/views/layouts/app.blade.php` | Master page shell: header, sidebar, footer, global JS/CSS | Layout |
| 4 | Layout — Header | `resources/views/layouts/header.blade.php` | Top bar: search, notifications, user profile with logout | Layout |
| 5 | Layout — Sidebar | `resources/views/layouts/sidebar.blade.php` | Role-gated navigation menu | Layout |
| 6 | Layout — Footer | `resources/views/layouts/footer.blade.php` | Copyright footer | Layout |
| 7 | Branch / Department List | `pages/authentication/branch/index.blade.php` | List departments (branch = department) with contact, address, status | Department |
| 8 | Branch / Department Create | `pages/authentication/branch/creatbranch.blade.php` | Add/Edit department modal (name, contact person, phone, address, city, state, pincode, status) | Department |
| 9 | Role List | `pages/authentication/roles/index.blade.php` | List roles with permissions count + Permission Matrix overview | Role |
| 10 | Role Create/Edit | `pages/authentication/roles/createrole.blade.php` | Add/Edit role with module-grouped permission checkboxes (many-to-many) | Role / Permission |
| 11 | User List | `pages/authentication/users/index.blade.php` | User directory with role, department, mobile, status + Role Permissions overview | User |
| 12 | User Create/Edit | `pages/authentication/users/createuser.blade.php` | Add/Edit user modal (name, role, branch, mobile, profile image, email, password) | User |
| 13 | Customer Directory | `pages/customers.blade.php` | List customers/companies with mineral, district, mobile, status; view/edit profile; add customer modal | Customer |
| 14 | Lease Application List | `pages/lease_application/customer.blade.php` | Application list with application no, client, district, category, documents, status | Lease Application |
| 15 | Application View | `pages/lease_application/viewapplication.blade.php` | Detailed application: doc checklist, folders, process flow stepper, activity timeline, MIMAS info | Lease Application |
| 16 | Application Step 1 | `pages/lease_application/createstep1.blade.php` | Wizard: Client name + district | Lease Application |
| 17 | Application Step 2 | `pages/lease_application/createstep2.blade.php` | Wizard: Contact person name + mobile | Lease Application |
| 18 | Application Step 3 | `pages/lease_application/createstep3.blade.php` | Wizard: Category under rule (MDCC, Rule 12/19/19-A/36-F/44/7) | Lease Application / Category |
| 19 | Application Step 4 | `pages/lease_application/createstep4.blade.php` | Wizard: Folders (Documents, Lease Application, Plan) | Lease Application / Folder |
| 20 | Application Step 5 | `pages/lease_application/createstep5.blade.php` | Wizard: Document upload checklist (16 items across 3 folders) | Document Checklist |
| 21 | Application Step 6 | `pages/lease_application/createstep6.blade.php` | Wizard: MIMAS registration details (user ID, password, email, contact) | MIMAS |
| 22 | Application Step 7 | `pages/lease_application/createstep7.blade.php` | Wizard: Preview & submit | Lease Application |
| 23 | Mining Plan List | `pages/mining-portal/index.blade.php` | Application list: client, district, mineral, plan type, stage | Mining Application |
| 24 | New Mining Application | `pages/mining-portal/newapplication.blade.php` | 6-step intake: Client, District-wise, Minerals, Plans, Folders, Preview | Mining Application |
| 25 | Project Folders | `pages/mining-portal/projectfolder.blade.php` | Folder structure overview with upload progress per folder | Mining Folder |
| 26 | Documents Manager | `pages/mining-portal/document.blade.php` | Folder tabs + document checklist upload/status per folder | Document Checklist |
| 27 | Process Flow | `pages/mining-portal/process.blade.php` | Validation workflow track (Upload→Validate→Approve→Reports→Archive→Logout) + audit log | Mining Application / Audit |
| 28 | Environment B1 Index | `pages/enviro_b1/index.blade.php` | Sub-category overview with folder progress + recent activity | Environment Project |
| 29 | Environment B1 Subcat 1 | `pages/enviro_b1/subcat1.blade.php` | Site & Mining Documentation folder tabs + document checklist | Environment Project / Document |
| 30 | Environment B1 Subcat 2 | `pages/enviro_b1/subcat2.blade.php` | EIA & TNPCB submission folder tabs + document checklist | Environment Project / Document |
| 31 | Environment B2 List | `pages/enviro_b2/index.blade.php` | B2 clearance applications list | Environment Project |
| 32 | Environment B2 Wizard | `pages/enviro_b2/wizard.blade.php` | 7-step B2 document workflow | Environment Project |
| 33 | Environment B2 Show | `pages/enviro_b2/show.blade.php` | B2 project detail: checklist, document upload/review modals, status selector, activity log | Environment Project |
| 34 | EC Certificate List | `pages/ec_certificate/index.blade.php` | EC certificates issued list | EC Certificate |
| 35 | EC Certificate Wizard | `pages/ec_certificate/wizard.blade.php` | 6-step EC issuance: Parivesh approval, download, view/print, store, communicate, preview | EC Certificate |
| 36 | PPT Department List | `pages/ppt_department/index.blade.php` | PPT department applications list | PPT Application |
| 37 | PPT Department Wizard | `pages/ppt_department/wizard.blade.php` | 7-step PPT online domain workflow (11 folders) | PPT Application |
| 38 | DGPS Survey List | `pages/dgps_survey/index.blade.php` | DGPS survey requests list | DGPS Survey |
| 39 | DGPS Survey Wizard | `pages/dgps_survey/wizard.blade.php` | 6-step DGPS survey workflow | DGPS Survey |
| 40 | Drone Survey List | `pages/drone_survey/index.blade.php` | Drone survey requests list | Drone Survey |
| 41 | Drone Survey Wizard | `pages/drone_survey/wizard.blade.php` | 6-step drone survey workflow | Drone Survey |
| 42 | Master — Unit List | `pages/master/unit/index.blade.php` | Units lookup list + create/edit/delete modal | Unit (master) |
| 43 | Master — Unit Create | `pages/master/unit/createunit.blade.php` | Add/Edit unit modal | Unit (master) |
| 44 | Master — Category List | `pages/master/category/index.blade.php` | Product categories list + create/edit/delete modal | Category (master) |
| 45 | Master — Category Create | `pages/master/category/createcat.blade.php` | Add/Edit category modal (cat_code, cat_name) | Category (master) |
| 46 | Master — Product List | `pages/master/product/index.blade.php` | Product list with branch, barcode, GST, cost %, MRP, unit, qty, discounts, category | Product |
| 47 | Master — Product Create | `pages/master/product/createproduct.blade.php` | Add/Edit product modal | Product |
| 48 | Master — Product Stock List | `pages/master/productstock/index.blade.php` | Product stock table (total, available, sales stock) | Product Stock |

---

## Implementation Status Legend

- **STATIC UI** — The page is a pure HTML frontend demo with no backend logic beyond rendering (e.g., customers, mining-portal pages, lease steps, PPT, EC, DGPS, Drone, enviro_b1).
- **WORKING CRUD** — The page connects to a real controller/model with AJAX (e.g., branch, role, user, category, product, product stock, unit view).
- **PARTIAL BACKEND** — Some pages have real backend (Environment B2 show/upload/review/status), others are UI-only wizards.

This distinction is critical: the database design must cover **all** entity data implied by the UI, not just the currently-wired modules.

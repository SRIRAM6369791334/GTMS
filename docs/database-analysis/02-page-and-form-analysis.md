# 02 - Page-by-Page and Form Analysis

This document analyzes each page and the forms it contains, identifying the purpose of each form and the entities it touches.

---

## 2.1 Login Page (`pages/login.blade.php`)

### Form: Login Form
- **Entity:** User / Authentication
- **Purpose:** Authenticate a system user.

| UI Label | Input Name | Type |
| -------- | ---------- | ---- |
| Email / User ID | email | text |
| Password | password | password |
| Remember me | remember | checkbox |

---

## 2.2 Dashboard (`pages/index.blade.php`)

- **Entity:** Dashboard / Aggregate
- **Purpose:** Show region-wide KPIs. No data entry — derived stats cards + charts + recent activity.
- `pages/customers.blade.php` etc. show hardcoded counts — these are **derived/aggregate values**, not stored data.

---

## 2.3 Department / Branch (`authentication/branch/index.blade.php`, `creatbranch.blade.php`)

### Form: Add Department (modal `#brancheadd`)
| UI Label | Input Name | Type | Required |
| -------- | ---------- | ---- | -------- |
| Department Name | branch_name | text | Yes |
| Contact Person | contact_person | text | Yes |
| Phone Number | mobile | text | Yes |
| Address | address | textarea | Yes |
| City | city | text | No |
| State | state | text | No |
| Pincode | pincode | text | No |
| Status | status (edit only) | select | No |

---

## 2.4 Role (`authentication/roles/index.blade.php`, `createrole.blade.php`)

### Form: Add Role (modal `#rolesadd`) / Edit Role (`#rolesedit`)
| UI Label | Input Name | Type | Required |
| -------- | ---------- | ---- | -------- |
| Role Name | name | text | Yes |
| Module Permissions | permissions[] | checkbox (many) | No |

**Multi-select indication:** Many-to-many role ↔ permission relationship.

---

## 2.5 User (`authentication/users/index.blade.php`, `createuser.blade.php`)

### Form: Add User (modal `#useradd`) / Edit User (`#useredit`)
| UI Label | Input Name | Type | Required |
| -------- | ---------- | ---- | -------- |
| Name | name | text | Yes |
| Role | role_id | select | Yes |
| Branch | branch_id | select | No |
| Mobile Number | mobile_num | text | No |
| Profile Image | image | file | No |
| Email | email | email | Yes |
| Password | password | password | Yes |
| Status | status | (from index) | No |

---

## 2.6 Customer (`pages/customers.blade.php`)

### Form: Add Customer (modal `#customeradd`)
| UI Label | Input Name | Type | Required |
| -------- | ---------- | ---- | -------- |
| Customer / Representative Name | customer_name | text | Yes |
| Company / Quarry Name | company_name | text | Yes |
| Mobile Number | mobile_num | text | Yes |
| Email Address | email | email | Yes |
| District | district | select | Yes |
| Mineral Type | mineral | select | Yes |
| GSTIN | gstin | text | No |
| PAN Number | pan | text | Yes |
| Quarry Area (Ha) | area | text | No |
| Status | status | select | Yes |
| Registered Quarry Address | (no name) | textarea | No |

### Form: Edit Customer (modal `#editCustomerModal`)
Same fields as Add (representative name, company, mobile, email, district, status).

---

## 2.7 Lease Application — Create Wizard

### Step 1 Form (`createstep1.blade.php`) — Entity: Lease Application / Customer
| UI Label | Input Name | Type | Required |
| -------- | ---------- | ---- | -------- |
| Client Name | (none) | text | Yes |
| District | (none) | select | Yes |

### Step 2 Form (`createstep2.blade.php`)
| UI Label | Input Name | Type | Required |
| -------- | ---------- | ---- | -------- |
| Contact Person Name | (none) | text | Yes |
| Mobile Number | (none) | text | Yes |

### Step 3 Form (`createstep3.blade.php`)
Radio-card selection of **Category Under Rule** (MDCC, Rule 12, Rule 19(1), Rule 19(2)(a), Rule 19-A, Rule 36-F, Rule 44, Rule 7).

### Step 4 Form (`createstep4.blade.php`)
Folder display (Documents, Lease Application, Plan) — no input.

### Step 5 Form (`createstep5.blade.php`)
Document upload checklist (16 items). **Repeated/dynamic rows** per checklist item + drag-drop upload zone.

### Step 6 Form (`createstep6.blade.php`)
| UI Label | Input Name | Type | Required |
| -------- | ---------- | ---- | -------- |
| MIMAS User ID | (none) | text | Yes |
| MIMAS Password | (none) | password | Yes |
| MIMAS Email ID | (none) | email | Yes |
| MIMAS Contact Number | (none) | text | Yes |

### Step 7 Preview
Read-only summary.

---

## 2.8 Mining Plan

### Mining Application List (`mining-portal/index.blade.php`)
Table columns: Client, District, Mineral, Plan type, Stage, Actions (View/Approve/Reject).

### New Mining Application (`mining-portal/newapplication.blade.php`)
| Step | Fields |
| ---- | ------ |
| 1 Client | Client/firm name, Applicant type (Individual/Partnership/Pvt Ltd/Trust), Mobile, Email, Registered address |
| 2 District | District, Taluk, Village, Survey number(s) |
| 3 Minerals | Radio cards (Rough Stone, Gravel, Granite, Lime Stone, Fire Clay, Others) |
| 4 Plan | Radio (Mining Plan, Revised, Modified, Scheme) |
| 5 Folders | Displays 6 folders (Field Log, Documents, Site Photos, Report, Plan, Others) |
| 6 Preview | Summary |

### Project Folders (`mining-portal/projectfolder.blade.php`)
Shows 6 folders with **upload progress**, each linking to the document manager. "Send for validation" action.

### Documents Manager (`mining-portal/document.blade.php`)
Folder tabs; each folder shows a **checklist of documents** with status (Uploaded / Pending / Rejected). **Drop-zone file upload.**

Document statuses seen: `Uploaded`, `Pending`, `Rejected`, `Verified`.

### Process Flow (`mining-portal/process.blade.php`)
Workflow stages: 6.1 Upload & Store, 6.2 Validate Data, 6.3 Approve Data, 6.4 Generate Reports, 6.5 Archive & Backup, 6.6 Logout. Includes validation checklist with Passed/Rejected status and **audit log**.

---

## 2.9 Environment Clearance B1

### B1 Index (`enviro_b1/index.blade.php`)
Sub-category overview with folder progress bars + recent activity feed.

### Subcat 1 (`enviro_b1/subcat1.blade.php` — Site & Mining Documentation)
Tabs: Documents (7), Report (11), GIS (1), Upload Signed Reports (1), PARIVESH Registration (4). Each tab has a document checklist with status + upload actions.

### Subcat 2 (`enviro_b1/subcat2.blade.php` — EIA & TNPCB)
Tabs: ToR Letter, Baseline Study, Draft, TNPCB Submission, Final EIA Report, Uploading File.

---

## 2.10 Environment Clearance B2 (`enviro_b2/*`)

### B2 List (`enviro_b2/index.blade.php`)
Table: Application No, Client, Project/Location, Category, Documents count, Process status, Updated, View.

### B2 Wizard (`enviro_b2/wizard.blade.php`) — 7 steps
| Step | Fields |
| ---- | ------ |
| 1 Applicant | Client/Applicant Name, Project/Quarry Name, District, Contact Number |
| 2 B2 Category | B2 vs B1 selection |
| 3 Folders | 6 folders (Documents, Site Photographs, Report, GIS, Signed Reports, PARIVESH) |
| 4 Documents | 29-item checklist with upload |
| 5 Validation | Review checklist |
| 6 Approval | Approve Data / Generate Reports / Archive |
| 7 Preview | Summary |

### B2 Show (`enviro_b2/show.blade.php`) — **WORKING BACKEND**
- Project header: name, code, client, location, status (draft/validation/approved/reported/archived).
- Checklist documents per folder with status (`pending`, `uploaded`, `validated`, `approved`, `revision_required`), file link, uploaded_at.
- **Upload modal** (file) and **Review modal** (status + review_note).
- Process flow list + **Activity log** (action, details, created_at).

---

## 2.11 EC Certificate (`ec_certificate/*`)

### EC List (`ec_certificate/index.blade.php`)
Table: EC Ref No, Applicant, Project, Parivesh status, Certificate status, Communication status, Updated.

### EC Wizard (`ec_certificate/wizard.blade.php`) — 6 steps
| Step | Fields |
| ---- | ------ |
| 1 | EC App Reference, Parivesh Application No, Applicant Name, Approval Date |
| 2 | Download EC certificate |
| 3 | View/Print |
| 4 | Store (EC Certificate + Final Project Docs folders) |
| 5 | Communication Type (Grant/Rejection), Recipient Email, Communication Note |
| 6 | Preview |

---

## 2.12 PPT Department (`ppt_department/*`)

### PPT List (`ppt_department/index.blade.php`)
Table: App No, Client Name, District, Mineral, Folders, Status, Updated.

### PPT Wizard (`ppt_department/wizard.blade.php`) — 7 steps
| Step | Fields |
| ---- | ------ |
| 1 | Client Name, Project/Quarry Name, Mobile, Email |
| 2 | District, Taluk/Village |
| 3 | Mineral (Rough Stone, Gravel, Granite, Lime Stone, Fire Clay, Others) |
| 4 | 11 folders (Documents, EDS & EDS Reply, Demand Note, File No, SEAC Agenda, SEAC Minutes, ADS, CER Affidavit, SEIAA Agenda, SEIAA Minutes, Environmental Clearance) |
| 5 | Document upload |
| 6 | Approve / Reports / Archive |
| 7 | Preview |

---

## 2.13 DGPS Survey (`dgps_survey/*`)

### DGPS List
Table: Survey No, Client, Location, Area, Survey Status, Report status, Updated.

### DGPS Wizard — 6 steps
| Step | Fields |
| ---- | ------ |
| 1 | Survey Request No, Client/Applicant, Lease Area, Location |
| 2 | Survey Date, Survey Team |
| 3 | Data Processing (field points, coordinates, area calc, map) |
| 4 | Survey Report |
| 5 | Report Upload in GTM Portal |
| 6 | Preview |

---

## 2.14 Drone Survey (`drone_survey/*`)

### Drone List
Table: Survey No, Client, Location, Lease Area, Survey Status, Deliverables, Updated.

### Drone Wizard — 6 steps
| Step | Fields |
| ---- | ------ |
| 1 | Survey Request No, Client/Applicant, Lease Area, Location |
| 2 | Flight Date, Drone/Pilot Details |
| 3 | Data processing (stitching, orthomosaic, contours, 3D) |
| 4 | Deliverables (Orthomosaic, Contour, 3D Model, Report) |
| 5 | Report Upload in GTMS Portal |
| 6 | Preview |

---

## 2.15 Master Data

### Unit (`master/unit/*`)
Form: Unit Name (text, required).

### Category (`master/category/*`)
Form: Category Code (text, required), Category Name (text, required).

### Product (`master/product/*`)
| UI Label | Input Name | Type | Required |
| -------- | ---------- | ---- | -------- |
| Branch | branch_id | select | Yes |
| Category | cat_id | select | Yes |
| Product Name | pro_name | text | Yes |
| GST | gst | text | Yes |
| Cast % | cast_per | text | Yes |
| MRP | mrp | text | Yes |
| Quantity | qty | text | Yes |
| Discount R | discount_1 | text | No |
| Discount G | discount_2 | text | No |
| Discount Y | discount_3 | text | No |

### Product Stock (`master/productstock/index.blade.php`)
Read-only table: Bar Code, Name, Total Stock, Available Stock, Sales Stock.

---

## 2.16 Layout / Global

### Header Search
Global search box (placeholder "Search here...") — no defined search scope. **NEEDS BUSINESS CONFIRMATION** for search behavior.

### Notification Bell / Message Modal (`app.blade.php`)
Static "Send Message" modal (Author, Email, Comment) — **demo/static, no backend**.

---

## Summary of Forms Requiring Data Storage

1. Login
2. Department (create/edit)
3. Role (create/edit + permissions)
4. User (create/edit)
5. Customer (create/edit)
6. Lease Application (7-step wizard + docs + MIMAS)
7. Mining Application (6-step intake)
8. Mining project folders / documents
9. Environment B1 subcats (folders/documents/validation)
10. Environment B2 (project + documents + validation/review)
11. EC Certificate (issuance)
12. PPT Application (7-step + 11 folders/docs)
13. DGPS Survey (6-step)
14. Drone Survey (6-step)
15. Unit (master)
16. Category (master)
17. Product (master)
18. Product Stock

# 03 - Complete Input Field Analysis

This document catalogs **every** input field across the entire UI, with possible data types and required-ness. Fields inside modals, tabs, collapsibles, dynamic rows, and JS templates are all included.

---

## 3.1 Authentication & Users

### Login
| UI Label | Input Name | HTML Type | Required | Possible Data Type |
| -------- | ---------- | --------- | -------- | ------------------ |
| Email / User ID | email | text | Yes | string |
| Password | password | password | Yes | string (hashed) |
| Remember me | remember | checkbox | No | boolean |

### Add / Edit User
| UI Label | Input Name | HTML Type | Required | Possible Data Type |
| -------- | ---------- | --------- | -------- | ------------------ |
| Name | name | text | Yes | string(255) |
| Role | role_id | select | Yes | unsignedBigInteger FK |
| Branch | branch_id | select | No | unsignedBigInteger FK (nullable) |
| Mobile Number | mobile_num | text | No | string(15) |
| Profile Image | image | file | No | string (path) |
| Email | email | email | Yes | string(255) unique |
| Password | password | password | Yes(create)/No(edit) | string (hashed) |
| Status | status | (index/hidden) | No | tinyInteger 0/1 |

### Department (Branch)
| UI Label | Input Name | HTML Type | Required | Possible Data Type |
| -------- | ---------- | --------- | -------- | ------------------ |
| Department Name | branch_name | text | Yes | string(255) |
| Contact Person | contact_person | text | Yes | string(255) |
| Phone Number | mobile | text | Yes | string(15) |
| Address | address | textarea | Yes | text |
| City | city | text | No | string(255) |
| State | state | text | No | string(255) |
| Pincode | pincode | text | No | string(10) |
| Status | status | select | No | tinyInteger 0/1 |

### Role
| UI Label | Input Name | HTML Type | Required | Possible Data Type |
| -------- | ---------- | --------- | -------- | ------------------ |
| Role Name | name | text | Yes | string(255) unique |
| Module Permissions | permissions[] | checkbox (multi) | No | id[] (pivot) |

---

## 3.2 Customers

### Add / Edit Customer
| UI Label | Input Name | HTML Type | Required | Possible Data Type |
| -------- | ---------- | --------- | -------- | ------------------ |
| Customer / Representative Name | customer_name | text | Yes | string(255) |
| Company / Quarry Name | company_name | text | Yes | string(255) |
| Mobile Number | mobile_num | text | Yes | string(15) |
| Email Address | email | email | Yes | string(255) |
| District | district | select | Yes | FK to districts OR string |
| Mineral Type | mineral | select | Yes | string |
| GSTIN | gstin | text | No | string(15) |
| PAN Number | pan | text | Yes | string(10) |
| Quarry Area (Ha) | area | text | No | decimal |
| Status | status | select | Yes | tinyInteger/string |
| Registered Quarry Address | (address) | textarea | No | text |

---

## 3.3 Lease Application Wizard

### Step 1 — Application
| UI Label | Input Name | Type | Required | Data Type |
| -------- | ---------- | ---- | -------- | --------- |
| Client Name | (unnamed) | text | Yes | string(255) |
| District | (unnamed) | select | Yes | FK/string |

### Step 2 — Basic Info
| UI Label | Input Name | Type | Required | Data Type |
| -------- | ---------- | ---- | -------- | --------- |
| Contact Person Name | (unnamed) | text | Yes | string(255) |
| Mobile Number | (unnamed) | text | Yes | string(15) |

### Step 3 — Category (radio cards)
Category under rule: `MDCC`, `Rule 12(2-A)(a)`, `Rule 19(1)`, `Rule 19(2)(a)`, `Rule 19-A`, `Rule 36-F`, `Rule 44`, `Rule 7`.

### Step 4 — Folders (display only)
Folders: Documents(12), Lease Application(4), Plan.

### Step 5 — Document Upload
Dynamic checklist rows: document name, file, status. File types: PDF, JPG, PNG, max 10MB.

### Step 6 — MIMAS
| UI Label | Input Name | Type | Required | Data Type |
| -------- | ---------- | ---- | -------- | --------- |
| User ID | (unnamed) | text | Yes | string(255) (encrypted) |
| Password | (unnamed) | password | Yes | string (encrypted) |
| Email ID | (unnamed) | email | Yes | string(255) |
| Contact Number | (unnamed) | text | Yes | string(15) |

### Step 7 — Preview (read-only)
Survey No, Village/Taluk, Area Extent, Mineral Type, Lease Period.

---

## 3.4 Mining Application Intake

| Step | UI Label | Input Name | Type | Required | Data Type |
| ---- | -------- | ---------- | ---- | -------- | --------- |
| 1 | Client / Firm name | (unnamed) | text | Yes | string(255) |
| 1 | Applicant type | (unnamed) | select | Yes | string |
| 1 | Mobile number | (unnamed) | text | Yes | string(15) |
| 1 | Email address | (unnamed) | email | Yes | string(255) |
| 1 | Registered address | (unnamed) | textarea | Yes | text |
| 2 | District | (unnamed) | select | Yes | FK/string |
| 2 | Taluk | (unnamed) | text | No | string(255) |
| 2 | Village | (unnamed) | text | No | string(255) |
| 2 | Survey number(s) | (unnamed) | text | No | string |
| 3 | Mineral | mineral | radio | Yes | string |
| 4 | Plan type | plan | radio | Yes | string |

---

## 3.5 Environment B2 (working backend)

From `enviro_b2/show.blade.php` and `wizard.blade.php`:

### B2 Project (`environment-b2.store`)
| UI Label | Input Name | Type | Required | Data Type |
| -------- | ---------- | ---- | -------- | --------- |
| Client / Applicant Name | client_name | text | Yes | string(255) |
| Project / Quarry Name | project_name | text | Yes | string(255) |
| Location | location | text | No | string(255) |
| District | district | text | No | string(255) |
| Contact Name | contact_name | text | No | string(255) |
| Contact Phone | contact_phone | text | No | string(20) |
| Contact Email | contact_email | email | No | string(255) |

### B2 Document Upload (`environment-b2.documents.upload`)
| Field | Type | Data Type |
| ----- | ---- | --------- |
| file | file (pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx,zip) | file, max 10MB |

### B2 Document Review (`environment-b2.documents.review`)
| UI Label | Input Name | Type | Data Type |
| -------- | ---------- | ---- | --------- |
| Decision | status | select (validated/approved/revision_required) | string |
| Review note | review_note | textarea | text |

### B2 Project Status (`environment-b2.status`)
| UI Label | Input Name | Type | Data Type |
| -------- | ---------- | ---- | --------- |
| Status | status | select (draft/validation/approved/reported/archived) | string |

---

## 3.6 EC Certificate Wizard

| Step | UI Label | Input Name | Type | Required | Data Type |
| ---- | -------- | ---------- | ---- | -------- | --------- |
| 1 | EC Application Reference | (unnamed) | text | Yes | string |
| 1 | Parivesh Application No | (unnamed) | text | Yes | string |
| 1 | Applicant Name | (unnamed) | text | Yes | string(255) |
| 1 | Approval Date | (unnamed) | date | No | date |
| 2 | EC Certificate file | (unnamed) | file | No | file |
| 5 | Communication Type | (unnamed) | select (Grant/Rejection) | No | string |
| 5 | Recipient Email | (unnamed) | email | No | string(255) |
| 5 | Communication Note | (unnamed) | textarea | No | text |

---

## 3.7 PPT Department Wizard

| Step | UI Label | Input Name | Type | Required | Data Type |
| ---- | -------- | ---------- | ---- | -------- | --------- |
| 1 | Client Name | (unnamed) | text | Yes | string(255) |
| 1 | Project / Quarry Name | (unnamed) | text | Yes | string(255) |
| 1 | Mobile Number | (unnamed) | text | Yes | string(15) |
| 1 | Email | (unnamed) | email | No | string(255) |
| 2 | District | (unnamed) | select | Yes | FK/string |
| 2 | Taluk / Village | (unnamed) | text | No | string(255) |
| 3 | Mineral | (unnamed) | radio | Yes | string |

---

## 3.8 DGPS Survey Wizard

| Step | UI Label | Input Name | Type | Required | Data Type |
| ---- | -------- | ---------- | ---- | -------- | --------- |
| 1 | Survey Request No | (unnamed) | text | Yes | string |
| 1 | Client / Applicant | (unnamed) | text | Yes | string(255) |
| 1 | Lease Area | (unnamed) | text | No | decimal |
| 1 | Location | (unnamed) | text | No | string |
| 2 | Survey Date | (unnamed) | date | No | date |
| 2 | Survey Team | (unnamed) | text | No | string |
| 3 | (Data processing upload) | (unnamed) | file dropzone | No | file |
| 5 | (GTM report upload) | (unnamed) | file | No | file |

---

## 3.9 Drone Survey Wizard

| Step | UI Label | Input Name | Type | Required | Data Type |
| ---- | -------- | ---------- | ---- | -------- | --------- |
| 1 | Drone Survey Request No | (unnamed) | text | Yes | string |
| 1 | Client / Applicant | (unnamed) | text | Yes | string(255) |
| 1 | Lease Area | (unnamed) | text | No | decimal |
| 1 | Location | (unnamed) | text | No | string |
| 2 | Flight Date | (unnamed) | date | No | date |
| 2 | Drone / Pilot Details | (unnamed) | text | No | string |
| 3 | (Processing upload) | (unnamed) | file | No | file |
| 5 | (GTMS Deliverables upload) | (unnamed) | file | No | file |

---

## 3.10 Master Modules

### Unit
| UI Label | Input Name | Type | Required | Data Type |
| -------- | ---------- | ---- | -------- | --------- |
| Unit Name | units | text | Yes | string(100) |

### Category
| UI Label | Input Name | Type | Required | Data Type |
| -------- | ---------- | ---- | -------- | --------- |
| Category Code | cat_code | text | Yes | string(50) unique |
| Category Name | cat_name | text | Yes | string(255) unique |

### Product
| UI Label | Input Name | Type | Required | Data Type |
| -------- | ---------- | ---- | -------- | --------- |
| Branch | branch_id | select | Yes | FK |
| Category | cat_id | select | Yes | FK |
| Product Name | pro_name | text | Yes | string(255) |
| GST | gst | text | Yes | decimal(5,2) |
| Cast % | cast_per | text | Yes | decimal(10,2) |
| MRP | mrp | text | Yes | decimal(10,2) |
| Quantity | qty | text | Yes | integer |
| Discount R | discount_1 | text | No | decimal |
| Discount G | discount_2 | text | No | decimal |
| Discount Y | discount_3 | text | No | decimal |
| Unit | (derived: 'Nos') | hidden | No | string |

### Product Stock (display columns)
Bar Code, Name, Total Stock, Available Stock, Sales Stock.

---

## 3.11 Global / Layout

### Header Search (unscoped)
| Input Name | Type | Data Type |
| ---------- | ---- | --------- |
| (none) search keyword | text | string |

### Send Message Modal (`app.blade.php`, static)
| UI Label | Input Name | Type | Data Type |
| -------- | ---------- | ---- | --------- |
| Name | Author | text | string |
| Email | Email | text | string |
| Comment | comment | textarea | text |

> **Note:** The "Send Message" modal and the notification dropdowns are static demo content and should not be persisted.

---

## Field Type Legend
- **Persistent** fields → stored in DB tables (documented in 04 & 06).
- **Derived** fields → computed at runtime (e.g., Total Stock = available + sales).
- **Temporary** fields → search, filters, pagination, confirm-password, step navigation.

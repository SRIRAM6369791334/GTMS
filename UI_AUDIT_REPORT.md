# GTMS UI Audit & Visual Regression Baseline Report

## Executive Summary
This document establishes the comprehensive visual design and UX audit for all existing modules in the GTMS portal.
The **Lease Application Module (`/application` and `/viewapplication`)** serves as the golden design reference standard and remains 100% untouched.
All 27 accessible pages across 11 modules were captured across 4 standard viewports (Desktop 1440x900, Laptop 1366x768, Tablet 768x1024, Mobile 390x844).
Every screenshot was verified with the preloader explicitly removed, achieving 100% valid page content capture (0 preloader artifacts).

---

## 1. Discovered Modules & Route Inventory

| Module | Route | Viewport Coverage | Status | Preloader Cleared | File Size Range |
|---|---|---|---|---|---|
| **Dashboard** | `/dashboard` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 54.2 KB - 162.3 KB |
| **Lease Application (Ref)** | `/application` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 60.5 KB - 192.0 KB |
| **Lease Dossier (Ref)** | `/viewapplication?id=36` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 51.0 KB - 127.8 KB |
| **Customers** | `/customers` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 52.5 KB - 206.9 KB |
| **Customer Tracking** | `/customer-tracking` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 119.5 KB - 362.4 KB |
| **Mining Plans** | `/miningplan` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 63.0 KB - 231.4 KB |
| **Project Folder** | `/projectfolder` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 68.8 KB - 202.8 KB |
| **Mining Process** | `/process` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 63.0 KB - 248.8 KB |
| **Environment Clearance** | `/eviron` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 61.9 KB - 208.6 KB |
| **EC Certificate** | `/ec-certificate` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 62.5 KB - 150.9 KB |
| **EC Compliance** | `/ec-compliance` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 46.3 KB - 144.5 KB |
| **DGPS Survey** | `/dgps-survey` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 41.6 KB - 123.7 KB |
| **Drone Survey** | `/drone-survey` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 43.0 KB - 174.0 KB |
| **PPT Department** | `/ppt-department` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 41.0 KB - 185.2 KB |
| **Quotations** | `/accounts/quotations` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 70.3 KB - 224.3 KB |
| **Money Receipts** | `/accounts/receipts` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 60.6 KB - 156.7 KB |
| **Customer Ledger** | `/accounts/ledger` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 77.6 KB - 199.1 KB |
| **Financial Reports** | `/accounts/reports` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 75.2 KB - 170.0 KB |
| **Create Quotation** | `/accounts/quotations/create` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 71.4 KB - 172.7 KB |
| **Category Master** | `/category` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 28.3 KB - 71.8 KB |
| **Product Master** | `/product` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 32.4 KB - 77.7 KB |
| **Product Stock Master** | `/productstock` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 33.3 KB - 77.4 KB |
| **Unit Master** | `/unit` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 26.0 KB - 65.8 KB |
| **User Management** | `/user` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 60.8 KB - 185.5 KB |
| **Role Management** | `/roles` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 67.5 KB - 188.5 KB |
| **Branch Management** | `/branch` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 58.9 KB - 160.2 KB |
| **Notifications** | `/notifications` | Desktop, Laptop, Tablet, Mobile | HTTP 200 | Yes | 55.2 KB - 170.6 KB |

---

## 2. Approved Reference Design Characteristics (Lease Application)

1. **Surface & Palette Tokens**:
   - Deep Navy Primary: `#0F1E4D` with hover `#1E3A8A`
   - Ore Gold Accent: `#D97706` with tint `#fef3c7`
   - Sky/Cobalt Blue: `#2563EB` with tint `#eff6ff`
   - Emerald Success: `#059669` with tint `#ecfdf5`
   - Rose/Crimson Destructive: `#DC2626` with tint `#fee2e2`
   - Border Subtle: `#E2E8F0`, Surface: `#FFFFFF`, Surface Subtle: `#F8FAFC`
2. **Cockpit Header Suite**:
   - Border radius: `16px`, subtle box shadow `0 4px 14px 0 rgba(15, 23, 42, 0.05)`
   - 48x48 squircle brand icon (`linear-gradient(135deg, #0F1E4D 0%, #1E40AF 100%)`)
   - Title: `1.5rem`, font-weight `800`, letter-spacing `-0.02em`
   - Primary action button: `btn-app-primary` (deep navy gradient, 10px radius, 0.875rem, font-weight 600)
3. **Bento KPI Cards**:
   - 14px border radius, 48x48 squircle icon with soft background tint
   - Tabular numerals (`font-variant-numeric: tabular-nums`), font-size `1.625rem`, font-weight `800`
   - Micro sub-label and status chip with 2px accent top line
4. **Action Dock (`.app-action-dock` / `.table-action-group`)**:
   - Single-row flex container: `display: inline-flex; align-items: center; gap: 0.35rem; white-space: nowrap;`
   - 34x34px rounded-8 icon buttons with hover transition (no wrapped or multi-line buttons)
   - View = `#eff6ff` (border `#bfdbfe`, text `#1d4ed8`), Edit = `#fef3c7` (border `#fde68a`, text `#b45309`), Delete = `#fee2e2` (border `#fecaca`, text `#dc2626`)
5. **Popups, Modals & SweetAlert2**:
   - Custom styled SweetAlert with Deep Navy confirm buttons, soft borders, and contextual messages (no generic browser alerts or raw discordant `#3085d6` buttons).

---

## 3. Discovered Visual Defects in Remaining Modules

### A. Master Data Module (`/category`, `/unit`, `/product`, `/productstock`)
1. **Header & Action Buttons**:
   - Uses legacy `.btn-info` (harsh magenta `#D653C1`) with pill `.btn-rounded` geometry.
   - Action buttons in DataTable use `.btn-xs .sharp` (tiny 24px raw colored squares/circles) that look unaligned and harsh.
2. **Modals**:
   - Modal headers lack the GTMS Deep Navy header bar and modern input borders (`#E2E8F0` with focus ring `rgba(37, 99, 235, 0.12)`).
3. **SweetAlerts**:
   - Category and Unit delete functions in `public/js/ajax/category.js` and `product.js` use default SweetAlert colors (`confirmButtonColor: '#3085d6'`, `cancelButtonColor: '#d33'`) and contain copy-paste typos ("You won't be able to recover this role!").

### B. Surveys & Technical Operations (`/dgps-survey`, `/drone-survey`, `/ppt-department`)
1. **Missing Cockpit & KPI Suite**:
   - Lacks the top Bento metric cards present in Lease Application.
2. **Table Action Ergonomics**:
   - Action buttons wrap on tablet/laptop viewports due to lack of explicit minimum column width and `.table-action-group` styling.

### C. Authentication Pages (`/user`, `/roles`, `/branch`)
1. **Mismatched Add Buttons**:
   - Uses harsh `.btn-info` instead of `.btn-app-primary` Deep Navy gradient.
   - Breadcrumb styling is raw and unaligned with the cockpit suite.

### D. Accounts Module (`/accounts/quotations`, `/receipts`, `/ledger`)
1. **Data Density & Table Headers**:
   - Currency figures are not formatted using `tabular-nums`.
   - Action buttons on quotations index wrap onto two rows in narrow viewports.

---

## 4. Verification Proof & Inventory Path
All baseline screenshots are cataloged in `artifacts/ui-audit/baseline/` and `tests/e2e/screenshots/baseline/`.
Detailed JSON inventory: `artifacts/ui-audit/baseline/baseline_inventory.json`.
Preloader suppression was verified in every single screenshot before capture.

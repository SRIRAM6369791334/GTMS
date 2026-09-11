# 08 - Master & Lookup Tables

This document details every master/lookup table, its purpose, and its seed data derived from the UI.

**Confidence:** 🔵 CONFIRMED · 🟢 INFERRED · 🟡 NEEDS CONFIRMATION

---

## 8.1 Purpose of Master Tables
Master tables replace **repeated string values** (dropdowns, radio cards, checkbox lists) that appear identically across many UI screens. This achieves 3NF, guarantees consistency, and allows admin management.

---

## 8.2 Master Tables

### `permissions` 🔵
Permission keys shown in role create/edit grouped by module.

Seed (derive from middleware gates in `routes/web.php` and sidebar):
| Module | Permission Keys |
| ------ | --------------- |
| Dashboard | dashboard.view |
| Department | branch.view, branch.add, branch.edit, branch.delete |
| Role | roles.view, roles.add, roles.edit, roles.delete |
| Users | users.view, users.add, users.edit, users.delete |
| Application (Lease) | application.view, application.add, application.edit, application.delete |
| Mining | mining.view, mining.add, mining.edit, mining.delete |
| Environment | environment.view, environment.add, environment.edit, environment.delete |
| PPT | ppt.view, ppt.add, ppt.edit, ppt.delete |
| DGPS | dgps.view, dgps.add, dgps.edit, dgps.delete |
| Drone | drone.view, drone.add, drone.edit, drone.delete |
| Customers | customers.view, customers.add, customers.edit |

> **Exact keys NEEDS CONFIRMATION** against `routes/web.php` middleware — the sidebar lists gate names (e.g. `branch.view`) which are the authoritative source.

### `departments` / `branches` 🔵
The concept is called **Department** in the UI list but **Branch** in user/product forms. **NEEDS CONFIRMATION** whether one canonical table covers both (recommended) or two distinct entities.

### `districts` 🟡
Tamil Nadu districts for dropdowns. Seed: all 38 TN districts (e.g., Ariyalur, Chennai, Coimbatore, Cuddalore, Dharmapuri, Dindigul, Erode, Kallakurichi, Kanchipuram, Kanyakumari, Karur, Krishnagiri, Madurai, Mayiladuthurai, Nagapattinam, Namakkal, Nilgiris, Perambalur, Pudukkottai, Ramanathapuram, Ranipet, Salem, Sivaganga, Tenkasi, Thanjavur, Theni, Thiruvallur, Thiruvarur, Thoothukudi, Tiruchirappalli, Tirunelveli, Tirupathur, Tiruppur, Tiruvannamalai, Vellore, Viluppuram, Virudhunagar).

### `minerals` 🟢 (pending confirm)
| Seed Value |
| ---------- |
| Rough Stone |
| Gravel |
| Granite |
| Lime Stone |
| Fire Clay |
| Others |

### `lease_categories` 🟢 (pending confirm)
| Seed Value (category under rule) |
| --------------------------------- |
| MDCC |
| Rule 12(2-A)(a) |
| Rule 19(1) |
| Rule 19(2)(a) |
| Rule 19-A |
| Rule 36-F |
| Rule 44 |
| Rule 7 |

### `plan_types` 🟢 (pending confirm)
| Seed Value |
| ---------- |
| Mining Plan |
| Revised Mining Plan |
| Modified Mining Plan |
| Scheme of Mining |

### `applicant_types` 🟢 (pending confirm)
| Seed Value |
| ---------- |
| Individual |
| Partnership |
| Pvt Ltd |
| Trust |

### `modules` 🟢
| Seed Value (code) | Name |
| ----------------- | ---- |
| mining | Mining Application |
| lease | Lease Application |
| environment | Environment Clearance |
| ppt | PPT Department |
| dgps | DGPS Survey |
| drone | Drone Survey |
| ec | EC Certificate |

### `folders` 🟡 (global, module-keyed)
| module | Folder Name |
| ------ | ----------- |
| mining | Field Log |
| mining | Documents |
| mining | Site Photos |
| mining | Report |
| mining | Plan |
| mining | Others |
| lease | Documents |
| lease | Lease Application |
| lease | Plan |
| environment | Documents |
| environment | Site Photographs |
| environment | Report |
| environment | GIS |
| environment | Signed Reports |
| environment | PARIVESH |
| ppt | Documents |
| ppt | EDS & EDS Reply |
| ppt | Demand Note |
| ppt | File No |
| ppt | SEAC Agenda |
| ppt | SEAC Minutes |
| ppt | ADS |
| ppt | CER Affidavit |
| ppt | SEIAA Agenda |
| ppt | SEIAA Minutes |
| ppt | Environmental Clearance |

### `document_fields` 🟡 (checklist master, per folder)
Long finite lists (16 for lease, 26–29 for environment, plus mining/PPT items). To be seeded from each module's checklist UI. **Full enumeration NEEDS CONFIRMATION** by scraping each `subcat1/subcat2/document` blade.

### `units` 🔵 (inventory)
Seed may be empty/admin-managed (e.g., Nos).

### `product_categories` 🔵 (inventory)
Admin-managed.

---

## 8.3 Common Columns on all master tables
- `id` PK
- `status` tinyInteger (1=enabled, 0=disabled) where list needs disabling
- `created_at` / `updated_at`

---

## 8.4 Design Note — Folder & Document as Master
A single **global `folders`** + **global `document_fields`** pair (module/folder discriminated) avoids duplicating the same checklist in every project row. Projects store only **document_field_id** references via `project_documents`. This is the 3NF-correct design; requires business sign-off (doc 16).

# 11 — JavaScript & Frontend Analysis

> Index: [PROJECT_MASTER_INDEX.md](PROJECT_MASTER_INDEX.md). Views: [10-blade-view-documentation.md](10-blade-view-documentation.md).

## 1. Load order — VERIFIED (`resources/views/layouts/app.blade.php:546-629`)
`/vendor/global/global.min.js` (jQuery + Bootstrap bundle) → toastr (+ init) → sweetalert2 (+ init) → bootstrap-select → counter / waypoint → apexchart, chart.js → peity → `/js/dashboard/dashboard-1.js` → DataTables + Responsive + `/js/plugins-init/datatables.init.js` → owl-carousel → `/js/custom.min.js` → `/js/app.js` → `/js/admin.js` → `/js/dlabnav-init.js` → page `@stack` / inline scripts → `/js/gtms-open-with.js`.
The Vite bundle (`resources/js/app.js` → axios bootstrap) is **never loaded** (no `@vite` directive). jQuery, not axios, is the AJAX client.

## 2. First-party scripts

| File | Lines | Purpose | Backend endpoints | Status |
|---|---|---|---|---|
| `public/js/ajax/user.js` | 363 | User CRUD modals, password show/hide toggle | `useradd`, `useredit`, `userdelete` | VERIFIED (endpoints) |
| `public/js/ajax/role.js` | 367 | Role CRUD, permission matrix | `roleadd`, `roleupdate`, `roledelete`, `roles/{id}/permissions` | VERIFIED |
| `public/js/ajax/branch.js` | — | Branch CRUD | `branchadd`, `branchedit`, `branchdelete` | VERIFIED |
| `public/js/ajax/category.js` | — | Category CRUD | `categoryadd`, `categoryedit`, `categorydelete` | VERIFIED |
| `public/js/ajax/product.js` | 85 | Product create | `/productadd` | VERIFIED |
| `public/js/ajax/customer.js` | — | Customer CRUD + MIMAS lookup | `/customeradd`, `/customeredit`, `/customerdelete`, `/customers/…` | VERIFIED |
| `public/js/ajax/unit.js` | **0** | Empty | — | VERIFIED |
| `public/js/gtms-chunk-uploader.js` | 581 | `class GtmsChunkUploader`: 10 MB slices, up to 3 retries with exponential backoff (base 1.5 s), resume via `/status`, cancel, SHA-256 per chunk (when available), CSRF from `meta[name=csrf-token]` sent as `X-CSRF-TOKEN` | `/api/upload/chunk/{init,upload,status,assemble,cancel}` | VERIFIED (header + key lines) |
| `public/js/gtms-open-with.js` | 799 | "Open with" chooser for document links. For DWG it offers Autodesk Viewer; KML → Google Earth / Maps; Office → `ms-word:` / `ms-excel:` / `ms-powerpoint:` protocol handlers; PDF / Office → **Google Docs Viewer with the file URL** (`https://docs.google.com/viewer?url=...`) | none (client side) | VERIFIED |
| `public/js/app.js` | 193 | Global UI helpers (template) | — | Role only |
| `public/js/admin.js` | 75 | Admin helpers | — | Role only |
| `public/js/custom.js` / `custom.min.js` | 531 | Template behaviour | — | Third-party template |
| `public/js/settings.js` | 381 | Template theme settings | — | Third-party template |
| `public/js/dlabnav-init.js` | 110 | Sidebar / nav init | — | Third-party template |
| `public/js/plugins-init/*` (22), `public/js/dashboard/*` (4) | — | Template demo initialisers (calendar, flot, morris, jqvmap, …) | — | Not analysed; several are probably unused demo code (INFERRED) |

## 3. Page-level inline scripts — PARTIALLY VERIFIED
Most module behaviour lives in inline `<script>` blocks in the Blade views (AJAX counts are in [10](10-blade-view-documentation.md) §2). The largest are `lease_application/createstep5` (13 AJAX calls: upload, remove, requirement toggle, chunk flow), `viewapplication` (11: validate / approve / reject / document status / dossier upload / move to mining), `notifications/index` (5: mark read / unread / delete / clear) and `mining-portal/newapplication` (autosave + store).

## 4. Response contract the JS relies on — VERIFIED
* Master CRUD: `{status: 1|0, message, data}`. The JS checks `status == 1`; validation errors arrive as HTTP 422 with Laravel's `errors` bag.
* Module wizards: `{status: 1, message, redirect}`; the JS sets `window.location = redirect`.
* Chunk API: `{success: bool, upload_token, chunk_index, progress_percent, …}`.
* Notifications: `{success, message, unread_count}`.
Changing these shapes breaks the front end silently. No TypeScript or contract tests exist.

## 5. Frontend risks
* `gtms-open-with.js` sends document URLs to Google's viewer. If the NAS is reachable from the internet, that exposes the (unauthenticated, see [16](16-file-upload-storage-system.md)) file URL to a third party. On a LAN-only NAS, Google cannot fetch the file, so the option simply fails (INFERRED).
* Bootstrap Icons come from a CDN, so the UI needs internet access for icons.
* Inline scripts make a Content-Security-Policy impractical, and none is set in `docker/nginx.conf`.

# 🎨 GTMS Frontend UI Component Catalog & Design Tokens (UI/UX Pro Max)
> **Token-Saver Reference:** Copy-paste these certified CSS tokens, HTML component structures, and design elements to guarantee 100% visual consistency with the GTMS enterprise theme.

---

## 1. CSS Design Tokens & Variables

Add these tokens inside your page `<style>` tag or master CSS:

```css
:root {
    /* Surface & Backgrounds */
    --gtms-surface: #ffffff;
    --gtms-surface-subtle: #f8fafc;
    --gtms-surface-elevated: #ffffff;
    --gtms-border: #e2e8f0;
    --gtms-border-subtle: #edf2f7;

    /* Typography */
    --gtms-text-primary: #0f172a;
    --gtms-text-secondary: #475569;
    --gtms-text-muted: #94a3b8;

    /* Brand Primary Accent (Navy to Royal Blue Gradient) */
    --gtms-accent: #0F1E4D;
    --gtms-accent-hover: #1e3a8a;
    --gtms-accent-light: #eff6ff;
    --gtms-accent-subtle: rgba(15, 30, 77, 0.06);

    /* Status Accents */
    --gtms-emerald: #10b981;
    --gtms-emerald-light: #ecfdf5;
    --gtms-amber: #f59e0b;
    --gtms-amber-light: #fffbeb;
    --gtms-rose: #ef4444;
    --gtms-rose-light: #fef2f2;
    --gtms-indigo: #6366f1;
    --gtms-indigo-light: #eef2ff;

    /* Elevation & Shadows */
    --gtms-card-shadow: 0 4px 18px -2px rgba(15, 30, 77, 0.05), 0 2px 6px -1px rgba(15, 30, 77, 0.03);
}

/* Dark Mode Theme Overrides */
body[data-theme-version="dark"] {
    --gtms-surface: #181818;
    --gtms-surface-subtle: #202020;
    --gtms-surface-elevated: #242424;
    --gtms-border: #282828;
    --gtms-border-subtle: #222222;
    --gtms-text-primary: #f8fafc;
    --gtms-text-secondary: #cbd5e1;
    --gtms-text-muted: #64748b;
    --gtms-accent: #3b82f6;
    --gtms-accent-hover: #60a5fa;
    --gtms-accent-light: rgba(59, 130, 246, 0.12);
    --gtms-card-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.45);
}
```

---

## 2. Bento 4-KPI Metric Cards Grid

Use this 4-card responsive grid at the top of every dashboard or directory page:

```html
<div class="row g-3 mb-4">
    <!-- Card 1: Total Metric -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold small">Total Records</span>
                    <h2 class="fw-bold mb-0 mt-1" id="kpi_total">142</h2>
                    <span class="text-muted small"><i class="fa fa-database me-1 text-primary"></i> Statewide</span>
                </div>
                <div class="p-3 bg-primary-subtle text-primary rounded-3 fs-4">
                    <i class="fa fa-layer-group"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Active / Operational -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold small">Active</span>
                    <h2 class="fw-bold mb-0 mt-1 text-success" id="kpi_active">128</h2>
                    <span class="text-success small"><i class="fa fa-check-circle me-1"></i> Verified</span>
                </div>
                <div class="p-3 bg-success-subtle text-success rounded-3 fs-4">
                    <i class="fa fa-circle-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Inactive / Suspended -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold small">Inactive / Pending</span>
                    <h2 class="fw-bold mb-0 mt-1 text-warning" id="kpi_inactive">14</h2>
                    <span class="text-warning small"><i class="fa fa-clock me-1"></i> Under Review</span>
                </div>
                <div class="p-3 bg-warning-subtle text-warning rounded-3 fs-4">
                    <i class="fa fa-circle-pause"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Architecture / Jurisdiction -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted text-uppercase fw-semibold small">Jurisdictions</span>
                    <h2 class="fw-bold mb-0 mt-1 text-info">38</h2>
                    <span class="text-info small"><i class="fa fa-map-location-dot me-1"></i> Tamil Nadu</span>
                </div>
                <div class="p-3 bg-info-subtle text-info rounded-3 fs-4">
                    <i class="fa fa-building-columns"></i>
                </div>
            </div>
        </div>
    </div>
</div>
```

---

## 3. Avatar Squircle & Monospace Code Badges

Use for personnel, customer, and department identification:

```html
<style>
.gtms-avatar-squircle {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    background: linear-gradient(135deg, #0F1E4D 0%, #2563eb 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(15, 30, 77, 0.15);
    overflow: hidden;
}
.gtms-code-badge {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #475569;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 0.1rem 0.4rem;
    border-radius: 5px;
    font-family: monospace;
}
</style>

<div class="d-flex align-items-center gap-3">
    <div class="gtms-avatar-squircle">
        <!-- Either user initials OR profile image -->
        <span>SK</span>
    </div>
    <div>
        <div class="fw-bold text-dark">R. Kumaresan</div>
        <div class="d-flex align-items-center gap-2 mt-1">
            <span class="gtms-code-badge">LUK_001</span>
            <span class="text-muted small">sribala@example.com</span>
        </div>
    </div>
</div>
```

---

## 4. Glowing Status Pills (Active / Inactive / Rejected)

```html
<style>
.gtms-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.3rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.775rem;
    font-weight: 600;
}
.gtms-status-active {
    background: #ecfdf5;
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.25);
}
.gtms-status-inactive {
    background: #fffbeb;
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.25);
}
.gtms-status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
}
.gtms-status-active .gtms-status-dot {
    background: #10b981;
    box-shadow: 0 0 6px rgba(16, 185, 129, 0.6);
}
.gtms-status-inactive .gtms-status-dot {
    background: #f59e0b;
}
</style>

<!-- Active Status -->
<span class="gtms-status-pill gtms-status-active">
    <span class="gtms-status-dot"></span> Active
</span>

<!-- Inactive Status -->
<span class="gtms-status-pill gtms-status-inactive">
    <span class="gtms-status-dot"></span> Inactive
</span>
```

---

## 5. Unified Action Dock

Standard action buttons placed on the rightmost column of tables:

```html
<style>
.gtms-action-dock {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 3px;
}
.dock-btn {
    width: 32px;
    height: 32px;
    border-radius: 7px;
    border: none;
    background: transparent;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.15s ease;
}
.dock-btn:hover {
    transform: scale(1.08);
}
.dock-btn.dock-edit:hover {
    background: #f59e0b;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.35);
}
.dock-btn.dock-delete:hover {
    background: #ef4444;
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.35);
}
</style>

<div class="gtms-action-dock">
    <button type="button" class="dock-btn dock-edit" title="Edit Record" data-id="1">
        <i class="fa fa-pencil"></i>
    </button>
    <button type="button" class="dock-btn dock-delete" title="Delete Record" data-id="1">
        <i class="fa fa-trash"></i>
    </button>
</div>
```

---

## 6. Modal Dialog (Eliminating Dexignlabs Pink Traps)

Standard modal structure with gradient header and neutral theme-aware cancel button:

```html
<div class="modal fade" id="exampleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="modal-header text-white p-3 px-4" style="background: linear-gradient(135deg, #0F1E4D 0%, #1e3a8a 100%);">
                <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2">
                    <i class="fa fa-plus-circle"></i> Add Record
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <form id="recordForm">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-dark">Record Title <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="fa fa-tag"></i></span>
                            <input type="text" name="name" class="form-control border-start-0 ps-0" placeholder="Enter title..." required>
                        </div>
                    </div>
                </div>

                <!-- Footer (Theme-Aware Neutral Cancel Button) -->
                <div class="modal-footer bg-light p-3 px-4 border-top d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary px-3 rounded-3" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                        <i class="fa fa-check me-1"></i> Save Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
```

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
    /* ─── GTMS ACCOUNTS MODULE — STANDARDIZED DESIGN TOKENS (P0.1) ─── */
    :root {
        /* Brand Primary & Accents */
        --color-primary: #0F1E4D;          /* GTMS Signature Navy */
        --color-primary-hover: #1A327E;    /* Active / Focused Navy */
        --color-secondary: #1E3A8A;        /* Royal Blue Accent */
        --color-background: #F8FAFC;       /* Off-White Clean Slate */
        --color-surface: #FFFFFF;          /* Pure White Card Surface */
        
        /* High-Contrast Typography (WCAG 2.1 AA Compliant) */
        --color-text-main: #0F172A;        /* Slate 900 - Headings & Primary Labels */
        --color-text-body: #1E293B;        /* Slate 800 - Body & Table Rows */
        --color-text-muted: #475569;       /* Slate 600 - Helper & Meta Text (5.5:1 ratio) */
        
        /* Semantic Financial Status Accents */
        --color-success: #059669;          /* Emerald - Settled / Accepted / Paid */
        --color-success-bg: #ECFDF5;       /* Light Emerald */
        --color-warning: #D97706;          /* Amber - Draft / Partial / Pending Scrutiny */
        --color-warning-bg: #FEF3C7;       /* Light Amber */
        --color-danger: #DC2626;           /* Rose Red - Overdue / Void / Rejected */
        --color-danger-bg: #FEF2F2;        /* Light Red */
        --color-info: #0284C7;             /* Sky Blue - Sent to Client */
        --color-info-bg: #E0F2FE;          /* Light Sky Blue */
        
        /* Borders, Dividers & Focus Rings */
        --color-border: #E2E8F0;           /* Table & Card Border */
        --color-border-clean: #CBD5E1;     /* Input & Control Border */
        --color-border-focus: #1E3A8A;     /* Focus Ring Outline */
        --color-card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.03);

        /* ── Backward-Compatibility Aliases for Existing Views ── */
        --ct-navy: var(--color-primary);
        --ct-navy-hover: var(--color-primary-hover);
        --ct-accent-blue: var(--color-secondary);
        --ct-accent-emerald: var(--color-success);
        --ct-accent-amber: var(--color-warning);
        --ct-accent-red: var(--color-danger);
        --ct-border: var(--color-border);
        --ct-card-shadow: var(--color-card-shadow);

        --color-navy: var(--color-primary);
        --color-navy-light: var(--color-secondary);
        --color-navy-dark: #0A1435;
        --color-slate-dark: var(--color-text-main);
        --color-slate-body: var(--color-text-body);
        --color-slate-muted: var(--color-text-muted);
        --color-emerald: var(--color-success);
        --color-amber: var(--color-warning);
        --color-rose: var(--color-danger);
        --color-bg-subtle: var(--color-background);
    }

    /* ─── P0.2: FINANCIAL NUMBER TABULAR ALIGNMENT ─── */
    .financial-num,
    .financial-number,
    .tabular-nums,
    td.text-end,
    th.text-end,
    .calc-row span:last-child {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }

    /* ─── P0.3: INPUT DATE PICKER ESCAPE SHIELD ─── */
    .content-body input[type="date"] {
        position: relative !important;
    }
    .content-body input[type="date"]::-webkit-calendar-picker-indicator {
        position: static !important;
        cursor: pointer !important;
        background: initial !important;
        color: initial !important;
        opacity: 0.7 !important;
        padding: 0 !important;
        margin: 0 !important;
        width: auto !important;
        height: auto !important;
    }

    /* ─── WCAG AAA HIGH-CONTRAST TEXT & VISIBILITY REINFORCEMENTS ─── */
    .content-body .text-muted,
    .content-body small.text-muted,
    .content-body .small.text-muted {
        color: #334155 !important; /* Deep Slate 700 (9.5:1 WCAG AAA contrast ratio vs white) */
    }
    .content-body .text-secondary {
        color: #475569 !important; /* Neutral Slate 600 instead of Dexignlabs pink (#FFA7D7) */
    }
    .content-body .form-label {
        color: #0F172A !important; /* Slate 900 */
        font-weight: 700 !important;
    }
    .badge.bg-light.text-muted,
    .badge.bg-light.text-secondary {
        background-color: #F1F5F9 !important;
        color: #0F172A !important;
        border: 1px solid #94A3B8 !important;
        font-weight: 700 !important;
    }

    /* ─── P0.1: REUSABLE BUTTON TOKENS & STATES ─── */
    .btn-navy {
        background-color: var(--color-primary) !important;
        border-color: var(--color-primary) !important;
        color: #ffffff !important;
        transition: all 0.18s ease-in-out;
    }
    .btn-navy:hover,
    .btn-navy:focus {
        background-color: var(--color-primary-hover) !important;
        border-color: var(--color-primary-hover) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2) !important;
    }
    .btn-navy:disabled,
    .btn-navy.disabled {
        background-color: #94A3B8 !important;
        border-color: #94A3B8 !important;
        color: #F8FAFC !important;
        cursor: not-allowed !important;
        opacity: 0.8 !important;
        box-shadow: none !important;
    }

    /* ─── P0.5: ACTIVE FILTER CHIPS STYLES ─── */
    .active-filter-bar {
        background: #F8FAFC;
        border: 1px solid var(--color-border);
        border-radius: 9px;
        padding: 8px 14px;
        margin-bottom: 1rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }
    .active-filter-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--color-text-muted);
        display: inline-flex;
        align-items: center;
    }
    .active-filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #FFFFFF;
        border: 1px solid #CBD5E1;
        border-radius: 6px;
        padding: 4px 9px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--color-primary);
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        transition: all 0.15s ease;
    }
    .active-filter-chip:hover {
        border-color: var(--color-secondary);
        background-color: #F0F4FF;
    }
    .chip-remove-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        color: #64748B;
        text-decoration: none;
        transition: all 0.15s ease;
        line-height: 1;
    }
    .chip-remove-btn:hover {
        background-color: rgba(220, 38, 38, 0.12);
        color: #DC2626;
    }
    .btn-clear-filters {
        font-size: 0.78rem;
        font-weight: 600;
        color: #DC2626;
        text-decoration: none;
        padding: 3px 8px;
        border-radius: 5px;
        transition: all 0.15s ease;
    }
    .btn-clear-filters:hover {
        background-color: #FEE2E2;
        color: #991B1B;
    }

    /* ─── P0.1: KPI CARDS HOVER & SHADOWS ─── */
    .card-kpi {
        border-radius: 12px;
        transition: transform 0.16s ease, box-shadow 0.16s ease;
    }
    .card-kpi:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.1);
    }

    /* ─── P1.2: SELECT2 THEMED INPUTS & FILTERS ─── */
    .select2-container .select2-selection--single {
        height: 38px;
        border: 1px solid var(--color-border-clean);
        border-radius: 6px;
        padding: 4px 10px;
        display: flex;
        align-items: center;
        background: #FFFFFF;
        transition: all 0.15s ease-in-out;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        color: var(--color-text-main);
        font-weight: 500;
        font-size: 0.88rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 8px;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--color-secondary);
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.15);
        outline: none;
    }
    .select2-dropdown {
        border: 1.5px solid var(--color-border-clean);
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
        z-index: 1050;
    }
    .select2-results__option {
        padding: 7px 12px;
        font-size: 0.88rem;
        color: var(--color-text-main);
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: var(--color-primary);
        color: #FFFFFF;
    }
    .select2-search--dropdown .select2-search__field {
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid var(--color-border-clean);
        outline: none;
    }

    /* ─── P2.3: STICKY TABLE HEADERS ─── */
    .table-sticky thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background-color: var(--color-surface, #FFFFFF);
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
    }

    /* ─── P2.1: RESPONSIVE MOBILE CARD VIEW ─── */
    .account-mobile-card {
        background: #FFFFFF;
        border: 1px solid var(--color-border);
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 10px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        transition: transform 0.14s ease, box-shadow 0.14s ease;
    }
    .account-mobile-card:hover {
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    }
    .account-mobile-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #F1F5F9;
        padding-bottom: 8px;
        margin-bottom: 10px;
    }
    .account-mobile-card-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        padding: 3px 0;
        font-size: 0.85rem;
    }
    .account-mobile-card-label {
        color: var(--color-text-muted);
        font-weight: 500;
    }
    .account-mobile-card-value {
        color: var(--color-text-main);
        font-weight: 600;
        text-align: right;
    }
    .account-mobile-card-actions {
        display: flex;
        gap: 6px;
        border-top: 1px solid #F1F5F9;
        padding-top: 10px;
        margin-top: 8px;
        justify-content: flex-end;
    }

    /* ─── P1.3: ACTIONABLE EMPTY STATES ─── */
    .empty-state-box {
        padding: 40px 20px;
        text-align: center;
    }
    .empty-state-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background-color: #F1F5F9;
        color: var(--color-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 16px;
    }
    .empty-state-title {
        font-weight: 700;
        color: var(--color-text-main);
        font-size: 1.15rem;
        margin-bottom: 6px;
    }
    .empty-state-desc {
        color: var(--color-text-muted);
        font-size: 0.88rem;
        max-width: 420px;
        margin: 0 auto 18px auto;
    }
</style>

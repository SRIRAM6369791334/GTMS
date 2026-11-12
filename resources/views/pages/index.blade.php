@extends('layouts.app')
@section('title', 'Dashboard')
@section('main_content')

<style>
    /* ==========================================================================
       GTMS EXECUTIVE DASHBOARD (UI-UX-PRO-MAX STANDARD)
       ========================================================================== */

    /* Dashboard Header & Metadata */
    .dash-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.25rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }
    .dash-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #b45309;
        background: #fef3c7;
        padding: 0.25rem 0.65rem;
        border-radius: 20px;
        border: 1px solid #fde68a;
        margin-bottom: 0.5rem;
    }
    .dash-title {
        font-family: 'Sora', sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--navy-950);
        margin: 0 0 0.25rem 0;
        letter-spacing: -0.02em;
    }
    .dash-subtitle {
        color: var(--ink-500);
        font-size: 0.88rem;
        margin: 0;
    }
    .dash-meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.45rem 0.95rem;
        background: var(--paper-raised);
        border: 1px solid var(--ink-100);
        border-radius: 30px;
        font-size: 0.78rem;
        color: var(--ink-700);
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .live-dot-pulse {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseLive 2s infinite;
    }
    @keyframes pulseLive {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* Quick Action Workflow Ribbon */
    .quick-launch-bar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.35rem;
        margin-bottom: 1.5rem;
        scrollbar-width: thin;
    }
    .quick-action-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.42rem 0.85rem;
        background: var(--paper-raised);
        border: 1px solid var(--ink-100);
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--ink-700);
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .quick-action-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        color: var(--navy-950);
    }
    .quick-action-pill.pill-lease:hover { border-color: #3b82f6; background: rgba(59, 130, 246, 0.05); }
    .quick-action-pill.pill-dgps:hover { border-color: #0284c7; background: rgba(2, 132, 199, 0.05); }
    .quick-action-pill.pill-drone:hover { border-color: #7c3aed; background: rgba(124, 58, 237, 0.05); }
    .quick-action-pill.pill-env:hover { border-color: #16a34a; background: rgba(22, 163, 74, 0.05); }
    .quick-action-pill.pill-ec:hover { border-color: #d97706; background: rgba(217, 119, 6, 0.05); }

    /* Primary & Technical Stat Cards */
    .stat-card-link {
        text-decoration: none !important;
        display: block;
        height: 100%;
    }
    .dash-stat-card {
        background: var(--paper-raised);
        border: 1px solid var(--ink-100);
        border-radius: 12px;
        padding: 1.15rem 1.25rem;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        overflow: hidden;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease, border-color 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .dash-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        border-color: rgba(59, 130, 246, 0.35);
    }
    .stat-ic-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .dash-stat-card:hover .stat-ic-box {
        transform: scale(1.06);
    }
    .stat-info {
        flex-grow: 1;
        min-width: 0;
    }
    .stat-num {
        font-family: 'Sora', sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        line-height: 1.15;
        color: var(--navy-950);
        margin-bottom: 0.15rem;
    }
    .stat-label {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--ink-500);
        text-transform: uppercase;
        letter-spacing: 0.04em;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.25rem;
    }
    .stat-trend {
        font-size: 0.71rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Operations Matrix Cards */
    .dash-tech-card {
        background: var(--paper-raised);
        border: 1px solid var(--ink-100);
        border-radius: 12px;
        padding: 1.15rem 1.25rem;
        height: 100%;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .dash-tech-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }
    .dash-tech-card.card-dgps { border-left: 4px solid #0284c7; }
    .dash-tech-card.card-drone { border-left: 4px solid #7c3aed; }
    .dash-tech-card.card-env { border-left: 4px solid #16a34a; }
    .dash-tech-card.card-ec { border-left: 4px solid #d97706; }

    /* Surface Panel Container */
    .dash-panel {
        background: var(--paper-raised);
        border: 1px solid var(--ink-100);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .panel-title {
        font-family: 'Sora', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        color: var(--navy-950);
        margin: 0;
    }
    .panel-subtitle {
        font-size: 0.75rem;
        color: var(--ink-500);
        margin: 0;
    }

    /* Modern Table Design */
    .dash-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-bottom: 0;
    }
    .dash-table th {
        font-size: 0.69rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--ink-500);
        background: transparent;
        padding: 0.65rem 0.85rem;
        border-bottom: 1px solid var(--ink-100);
        white-space: nowrap;
    }
    .dash-table td {
        padding: 0.85rem 0.85rem;
        font-size: 0.82rem;
        color: var(--ink-700);
        border-bottom: 1px solid var(--ink-100);
        vertical-align: middle;
    }
    .dash-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .dash-table tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.03);
    }
    .dash-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Client Avatar Initial Chip */
    .client-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .client-avatar {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #e2e8f0;
        color: #1e293b;
        font-weight: 700;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid rgba(0,0,0,0.06);
    }

    /* Chip Badges */
    .chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.22rem 0.65rem;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        line-height: 1.2;
    }
    .chip-ok {
        background: #dcfce7;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    .chip-warn {
        background: #fef3c7;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .chip-danger {
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .chip-navy {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .survey-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .survey-badge-dgps {
        background: #e0f2fe;
        color: #0369a1;
        border: 1px solid #bae6fd;
    }
    .survey-badge-drone {
        background: #f3e8ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
    }

    /* Action Button in Tables */
    .btn-table-action {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.28rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 6px;
        border: 1px solid var(--ink-100);
        background: var(--paper-raised);
        color: var(--navy-950);
        text-decoration: none !important;
        transition: all 0.15s ease;
    }
    .btn-table-action:hover {
        background: #3b82f6;
        color: #ffffff;
        border-color: #3b82f6;
    }

    /* Chart Container & Doughnut Center Overlay */
    .chart-container-rel {
        position: relative;
        flex-grow: 1;
        min-height: 250px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .chart-donut-center {
        position: absolute;
        top: 44%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        pointer-events: none;
    }
    .chart-center-val {
        font-family: 'Sora', sans-serif;
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--navy-950);
        line-height: 1;
    }
    .chart-center-lbl {
        font-size: 0.68rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--ink-500);
        letter-spacing: 0.05em;
        margin-top: 0.2rem;
    }

    /* Activity Timeline */
    .activity-feed-modern {
        position: relative;
        padding-left: 22px;
    }
    .activity-feed-modern::before {
        content: '';
        position: absolute;
        top: 6px;
        bottom: 6px;
        left: 7px;
        width: 2px;
        background: var(--ink-100);
    }
    .activity-item-modern {
        position: relative;
        padding-bottom: 1.25rem;
    }
    .activity-item-modern:last-child {
        padding-bottom: 0;
    }
    .activity-dot-modern {
        position: absolute;
        left: -22px;
        top: 4px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--paper-raised);
        border: 2px solid #3b82f6;
        box-shadow: 0 0 0 2px var(--paper);
    }
    .activity-dot-modern.dot-ok { border-color: #10b981; }
    .activity-dot-modern.dot-danger { border-color: #ef4444; }
    .activity-dot-modern.dot-warn { border-color: #f59e0b; }

    /* Financial Cards Scoped */
    .fin-metric-box {
        padding: 1rem 1.15rem;
        border-radius: 10px;
        border: 1px solid var(--ink-100);
        background: var(--paper);
        transition: transform 0.15s ease;
    }
    .fin-metric-box:hover {
        transform: translateY(-1px);
    }
    .fin-invoiced {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    .fin-paid {
        background: #f0fdf4;
        border-color: #bbf7d0;
    }
    .fin-pending {
        background: #fffbeb;
        border-color: #fde68a;
    }

    /* District Snapshot Cards */
    .district-stat-card {
        padding: 0.95rem;
        border-radius: 10px;
        background: var(--paper);
        border: 1px solid var(--ink-100);
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        transition: transform 0.15s ease, border-color 0.15s ease;
    }
    .district-stat-card:hover {
        transform: translateY(-2px);
        border-color: #3b82f6;
    }

    /* Empty state display */
    .empty-state-wrap {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--ink-500);
    }

    /* ==========================================================================
       DARK MODE SPECIALIZED OVERRIDES
       ========================================================================== */
    body[data-theme-version="dark"] .dash-eyebrow {
        background: rgba(245, 158, 11, 0.12);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.25);
    }
    body[data-theme-version="dark"] .client-avatar {
        background: #2a2d36;
        color: #cbd5e1;
        border-color: rgba(255, 255, 255, 0.08);
    }
    body[data-theme-version="dark"] .dash-table tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.03);
    }
    body[data-theme-version="dark"] .chip-ok {
        background: rgba(34, 197, 94, 0.15);
        color: #4ade80;
        border-color: rgba(34, 197, 94, 0.25);
    }
    body[data-theme-version="dark"] .chip-warn {
        background: rgba(245, 158, 11, 0.15);
        color: #fbbf24;
        border-color: rgba(245, 158, 11, 0.25);
    }
    body[data-theme-version="dark"] .chip-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.25);
    }
    body[data-theme-version="dark"] .chip-navy {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border-color: rgba(59, 130, 246, 0.25);
    }
    body[data-theme-version="dark"] .survey-badge-dgps {
        background: rgba(2, 132, 199, 0.15);
        color: #38bdf8;
        border-color: rgba(2, 132, 199, 0.25);
    }
    body[data-theme-version="dark"] .survey-badge-drone {
        background: rgba(124, 58, 237, 0.15);
        color: #c084fc;
        border-color: rgba(124, 58, 237, 0.25);
    }
    body[data-theme-version="dark"] .fin-invoiced {
        background: rgba(59, 130, 246, 0.06);
        border-color: rgba(255, 255, 255, 0.08);
    }
    body[data-theme-version="dark"] .fin-paid {
        background: rgba(34, 197, 94, 0.08);
        border-color: rgba(34, 197, 94, 0.2);
    }
    body[data-theme-version="dark"] .fin-pending {
        background: rgba(245, 158, 11, 0.08);
        border-color: rgba(245, 158, 11, 0.2);
    }
    body[data-theme-version="dark"] .activity-dot-modern {
        box-shadow: 0 0 0 2px #202020;
    }
    body[data-theme-version="dark"] .dash-panel .text-dark,
    body[data-theme-version="dark"] .fin-metric-box .text-dark,
    body[data-theme-version="dark"] .district-stat-card .text-dark,
    body[data-theme-version="dark"] .dash-stat-card .text-dark,
    body[data-theme-version="dark"] .dash-table .text-dark,
    body[data-theme-version="dark"] .dash-meta-badge .text-dark {
        color: #f8fafc !important;
    }
    body[data-theme-version="dark"] .dash-panel .text-navy {
        color: #60a5fa !important;
    }
    body[data-theme-version="dark"] .district-stat-card {
        background: #1a1a24;
        border-color: rgba(255, 255, 255, 0.08);
    }
    body[data-theme-version="dark"] .badge.bg-light.text-dark {
        background: #2a2d36 !important;
        color: #e2e8f0 !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    body[data-theme-version="dark"] .btn-table-action {
        background: #202020;
        border-color: #3b4252;
        color: #cbd5e1;
    }
    body[data-theme-version="dark"] .btn-table-action:hover {
        background: #3b82f6;
        border-color: #3b82f6;
        color: #ffffff;
    }

    /* ==========================================================================
       VISUAL POLISH LAYER (look & feel only - layout and behaviour unchanged)
       ========================================================================== */
    .page {
        --polish-radius: 16px;
        --polish-shadow-sm: 0 1px 2px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.04);
        --polish-shadow-md: 0 2px 4px rgba(15, 23, 42, 0.04), 0 12px 28px -8px rgba(15, 23, 42, 0.12);
        --polish-ease: cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Greeting: a touch more presence */
    .dash-title {
        font-size: clamp(1.45rem, 1.1rem + 1.2vw, 1.95rem);
        background: linear-gradient(90deg, var(--navy-950) 0%, #1e40af 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .dash-subtitle { font-size: 0.9rem; }

    /* Quick launch pills: crisper, with a soft lift */
    .quick-action-pill {
        padding: 0.46rem 0.95rem;
        box-shadow: var(--polish-shadow-sm);
    }
    .quick-action-pill i { opacity: 0.85; }

    /* KPI cards: softer depth, accent line, labels never cut off */
    .dash-stat-card,
    .dash-tech-card {
        border-radius: var(--polish-radius);
        box-shadow: var(--polish-shadow-sm);
        transition: transform 0.25s var(--polish-ease), box-shadow 0.25s var(--polish-ease), border-color 0.25s ease;
    }
    .dash-stat-card::before {
        content: '';
        position: absolute;
        inset: 0 0 auto 0;
        height: 3px;
        background: linear-gradient(90deg, rgba(59, 130, 246, 0.0), rgba(59, 130, 246, 0.55), rgba(59, 130, 246, 0.0));
        opacity: 0;
        transition: opacity 0.25s ease;
    }
    .dash-stat-card:hover::before { opacity: 1; }
    .dash-stat-card:hover,
    .dash-tech-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--polish-shadow-md);
    }
    .stat-ic-box {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.04);
    }
    .stat-num {
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.02em;
    }
    .stat-label {
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
        line-height: 1.3;
        letter-spacing: 0.03em;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    /* Panels */
    .dash-panel {
        border-radius: var(--polish-radius);
        box-shadow: var(--polish-shadow-sm);
        padding: 1.35rem 1.5rem;
    }
    .panel-header { gap: 0.75rem; }
    .panel-title { font-size: 1.02rem; letter-spacing: -0.01em; }
    .panel-total-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.28rem 0.7rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #1e40af;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        white-space: nowrap;
    }

    /* Tables: scroll instead of clipping, sticky header, calmer rows */
    .dash-panel .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0 -0.5rem;
        padding: 0 0.5rem;
    }
    .dash-table th {
        position: sticky;
        top: 0;
        z-index: 1;
        background: var(--paper-raised);
    }
    .dash-table tbody tr:hover { background-color: rgba(59, 130, 246, 0.045); }
    .client-avatar {
        border-radius: 10px;
        background: linear-gradient(135deg, #e0e7ff 0%, #f1f5f9 100%);
        color: #1e3a8a;
    }
    .chip { padding: 0.24rem 0.7rem; }
    .btn-table-action { border-radius: 8px; }

    /* Keyboard focus that is visible but quiet */
    .stat-card-link:focus-visible .dash-stat-card,
    .quick-action-pill:focus-visible,
    .btn-table-action:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }

    /* Dark mode for the polish layer */
    body[data-theme-version="dark"] .dash-title {
        background: linear-gradient(90deg, #f8fafc 0%, #93c5fd 100%);
        -webkit-background-clip: text;
        background-clip: text;
    }
    body[data-theme-version="dark"] .panel-total-badge {
        color: #93c5fd;
        background: rgba(59, 130, 246, 0.12);
        border-color: rgba(59, 130, 246, 0.3);
    }
    body[data-theme-version="dark"] .client-avatar {
        background: linear-gradient(135deg, #1e293b 0%, #2a2d36 100%);
        color: #cbd5e1;
    }
    body[data-theme-version="dark"] .dash-stat-card,
    body[data-theme-version="dark"] .dash-tech-card,
    body[data-theme-version="dark"] .dash-panel {
        box-shadow: none;
    }

    /* Phones: two KPI cards per row are narrow, so stack icon above the text */
    @media (max-width: 575.98px) {
        .dash-stat-card,
        .dash-tech-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.65rem;
            padding: 1rem;
        }
        .stat-ic-box {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            font-size: 1.05rem;
        }
        .stat-num { font-size: 1.45rem; }
        .stat-label {
            font-size: 0.68rem;
            letter-spacing: 0.02em;
            -webkit-line-clamp: 3;
            overflow-wrap: anywhere;
        }
        .stat-trend { font-size: 0.68rem; flex-wrap: wrap; }
        .dash-panel { padding: 1.1rem 1rem; }
    }

    /* ==========================================================================
       KPI CARDS v2 - tone-coloured cards with entrance, hover and shine motion
       ========================================================================== */
    .row > div:nth-child(1) > .stat-card-link > .dash-stat-card { --tone: 37 99 235; }
    .row > div:nth-child(2) > .stat-card-link > .dash-stat-card { --tone: 217 119 6; }
    .row > div:nth-child(3) > .stat-card-link > .dash-stat-card { --tone: 22 163 74; }
    .row > div:nth-child(4) > .stat-card-link > .dash-stat-card { --tone: 100 116 139; }
    .dash-tech-card.card-dgps  { --tone: 2 132 199; }
    .dash-tech-card.card-drone { --tone: 124 58 237; }
    .dash-tech-card.card-env   { --tone: 22 163 74; }
    .dash-tech-card.card-ec    { --tone: 217 119 6; }

    .dash-stat-card,
    .dash-tech-card {
        --tone: 37 99 235;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.85rem;
        padding: 1.15rem 1.2rem 1.1rem;
        border: 1px solid rgb(var(--tone) / 0.14);
        background:
            radial-gradient(120% 90% at 100% 0%, rgb(var(--tone) / 0.10) 0%, transparent 55%),
            var(--paper-raised);
        overflow: hidden;
        isolation: isolate;
        opacity: 0;
        animation: kpiIn 0.6s var(--polish-ease) forwards;
    }
    /* (per-card selectors above are more specific than this default, so they win) */
    .dash-tech-card.card-dgps,
    .dash-tech-card.card-drone,
    .dash-tech-card.card-env,
    .dash-tech-card.card-ec { border-left: 1px solid rgb(var(--tone) / 0.14); }

    /* Staggered entrance */
    .row > div:nth-child(1) > .stat-card-link > .dash-stat-card,
    .row > div:nth-child(1) > .stat-card-link > .dash-tech-card { animation-delay: 0.05s; }
    .row > div:nth-child(2) > .stat-card-link > .dash-stat-card,
    .row > div:nth-child(2) > .stat-card-link > .dash-tech-card { animation-delay: 0.12s; }
    .row > div:nth-child(3) > .stat-card-link > .dash-stat-card,
    .row > div:nth-child(3) > .stat-card-link > .dash-tech-card { animation-delay: 0.19s; }
    .row > div:nth-child(4) > .stat-card-link > .dash-stat-card,
    .row > div:nth-child(4) > .stat-card-link > .dash-tech-card { animation-delay: 0.26s; }
    .dash-tech-card { animation-delay: 0.32s; }

    @keyframes kpiIn {
        from { opacity: 0; translate: 0 14px; }
        to   { opacity: 1; translate: 0 0; }
    }

    /* Tone bar on top: short at rest, full width on hover */
    .dash-stat-card::before,
    .dash-tech-card::before {
        content: '';
        position: absolute;
        inset: 0 0 auto 0;
        height: 3px;
        background: linear-gradient(90deg, rgb(var(--tone)), rgb(var(--tone) / 0.25));
        opacity: 1;
        transform-origin: left center;
        transform: scaleX(0.32);
        transition: transform 0.55s var(--polish-ease);
    }
    /* Light sweep on hover */
    .dash-stat-card::after,
    .dash-tech-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: -70%;
        width: 50%;
        height: 100%;
        background: linear-gradient(105deg, transparent 0%, rgba(255, 255, 255, 0.55) 50%, transparent 100%);
        transform: skewX(-18deg);
        pointer-events: none;
        transition: left 0.75s ease;
        z-index: 1;
    }
    .stat-card-link:hover .dash-stat-card::before,
    .stat-card-link:hover .dash-tech-card::before { transform: scaleX(1); }
    .stat-card-link:hover .dash-stat-card::after,
    .stat-card-link:hover .dash-tech-card::after { left: 130%; }

    .stat-card-link:hover .dash-stat-card,
    .stat-card-link:hover .dash-tech-card {
        transform: translateY(-5px);
        border-color: rgb(var(--tone) / 0.38);
        box-shadow: 0 20px 38px -16px rgb(var(--tone) / 0.45), 0 2px 6px rgba(15, 23, 42, 0.05);
    }
    .stat-card-link:active .dash-stat-card,
    .stat-card-link:active .dash-tech-card { transform: translateY(-2px) scale(0.99); }

    /* Solid gradient icon tile */
    .dash-stat-card .stat-ic-box,
    .dash-tech-card .stat-ic-box {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        font-size: 1.2rem;
        background: linear-gradient(135deg, rgb(var(--tone)) 0%, rgb(var(--tone) / 0.72) 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 10px 20px -8px rgb(var(--tone) / 0.6);
        transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .stat-card-link:hover .stat-ic-box { transform: translateY(-2px) rotate(-8deg) scale(1.08); }

    .dash-stat-card .stat-info,
    .dash-tech-card .stat-info { width: 100%; }
    .dash-stat-card .stat-num,
    .dash-tech-card .stat-num {
        font-size: 2rem;
        line-height: 1;
        margin-bottom: 0.35rem;
    }
    .dash-stat-card .stat-label,
    .dash-tech-card .stat-label {
        font-size: 0.72rem;
        color: var(--ink-500);
        margin-bottom: 0.6rem;
    }
    /* Status line as a tinted chip */
    .dash-stat-card .stat-trend,
    .dash-tech-card .stat-trend {
        padding: 0.22rem 0.6rem;
        border-radius: 999px;
        background: rgb(var(--tone) / 0.09);
        border: 1px solid rgb(var(--tone) / 0.16);
    }
    /* Arrow hint that the card opens a list */
    .dash-stat-card .stat-info::after,
    .dash-tech-card .stat-info::after {
        content: '\2192';
        position: absolute;
        right: 1.1rem;
        bottom: 1rem;
        font-weight: 700;
        color: rgb(var(--tone));
        opacity: 0;
        transform: translateX(-6px);
        transition: opacity 0.25s ease, transform 0.35s var(--polish-ease);
    }
    .stat-card-link:hover .stat-info::after { opacity: 1; transform: translateX(0); }

    body[data-theme-version="dark"] .dash-stat-card,
    body[data-theme-version="dark"] .dash-tech-card {
        border-color: rgb(var(--tone) / 0.22);
        background:
            radial-gradient(120% 90% at 100% 0%, rgb(var(--tone) / 0.16) 0%, transparent 60%),
            var(--paper-raised);
    }
    body[data-theme-version="dark"] .dash-stat-card::after,
    body[data-theme-version="dark"] .dash-tech-card::after {
        background: linear-gradient(105deg, transparent 0%, rgba(255, 255, 255, 0.10) 50%, transparent 100%);
    }
    body[data-theme-version="dark"] .dash-stat-card .stat-trend.text-warning-emphasis,
    body[data-theme-version="dark"] .dash-tech-card .stat-trend.text-warning-emphasis { color: #fbbf24 !important; }
    body[data-theme-version="dark"] .dash-stat-card .stat-trend.text-muted { color: #94a3b8 !important; }

    /* ==========================================================================
       TABLES & CHART PANEL - motion and hover
       ========================================================================== */
    .dash-panel {
        transition: border-color 0.25s ease, box-shadow 0.3s var(--polish-ease);
        animation: kpiIn 0.6s var(--polish-ease) 0.35s both;
    }
    .dash-panel:hover {
        border-color: rgba(59, 130, 246, 0.25);
        box-shadow: var(--polish-shadow-md);
    }
    .dash-table tbody tr {
        animation: rowIn 0.45s var(--polish-ease) both;
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
    }
    .dash-table tbody tr:nth-child(1) { animation-delay: 0.45s; }
    .dash-table tbody tr:nth-child(2) { animation-delay: 0.52s; }
    .dash-table tbody tr:nth-child(3) { animation-delay: 0.59s; }
    .dash-table tbody tr:nth-child(4) { animation-delay: 0.66s; }
    .dash-table tbody tr:nth-child(5) { animation-delay: 0.73s; }
    .dash-table tbody tr:nth-child(n+6) { animation-delay: 0.8s; }
    @keyframes rowIn {
        from { opacity: 0; translate: -8px 0; }
        to   { opacity: 1; translate: 0 0; }
    }
    .dash-table tbody tr:hover {
        background-color: rgba(59, 130, 246, 0.05);
        box-shadow: inset 3px 0 0 #3b82f6;
    }
    .dash-table tbody tr .client-avatar { transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .dash-table tbody tr:hover .client-avatar { transform: scale(1.1) rotate(-4deg); }
    .dash-table tbody tr .chip { transition: transform 0.2s ease; }
    .dash-table tbody tr:hover .chip { transform: translateY(-1px); }
    .btn-table-action {
        transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease, box-shadow 0.25s ease, transform 0.2s ease;
    }
    .btn-table-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 16px -6px rgba(59, 130, 246, 0.55);
    }
    .chart-container-rel canvas { transition: filter 0.3s ease; }
    .chart-center-val { font-variant-numeric: tabular-nums; }
    body[data-theme-version="dark"] .dash-table tbody tr:hover {
        background-color: rgba(96, 165, 250, 0.07);
        box-shadow: inset 3px 0 0 #60a5fa;
    }
    body[data-theme-version="dark"] .dash-panel:hover { border-color: rgba(96, 165, 250, 0.3); }

    /* ==========================================================================
       GLASS / MODERN THEME + SPACING SYSTEM (whole dashboard)
       ========================================================================== */
    .page {
        --space: 1.25rem;                 /* one gap size for the whole page */
        --glass-bg: rgba(255, 255, 255, 0.62);
        --glass-bg-soft: rgba(255, 255, 255, 0.48);
        --glass-border: rgba(255, 255, 255, 0.75);
        --glass-edge: rgba(15, 23, 42, 0.06);
        --glass-shadow: 0 10px 30px -12px rgba(30, 41, 99, 0.14), inset 0 1px 0 rgba(255, 255, 255, 0.85);
        --glass-blur: blur(16px) saturate(160%);
    }

    /* Soft colour mesh behind the glass */
    .content-body:has(> .container-fluid > main.page) {
        background:
            radial-gradient(40rem 26rem at 8% -4%, rgba(59, 130, 246, 0.13), transparent 60%),
            radial-gradient(34rem 24rem at 96% 6%, rgba(139, 92, 246, 0.11), transparent 60%),
            radial-gradient(38rem 28rem at 60% 62%, rgba(20, 184, 166, 0.08), transparent 60%),
            radial-gradient(30rem 22rem at 0% 100%, rgba(245, 158, 11, 0.07), transparent 60%);
        background-attachment: fixed;
    }

    /* Spacing: identical gaps between every section and every card */
    .page .row { --bs-gutter-x: var(--space); --bs-gutter-y: var(--space); }
    .page > .row { margin-bottom: var(--space) !important; }
    .page > .row:last-child { margin-bottom: calc(var(--space) * 1.5) !important; }
    .dash-head { margin-bottom: var(--space); }
    .quick-launch-bar { margin-bottom: var(--space); }

    /* Glass surfaces */
    .dash-panel,
    .dash-stat-card,
    .dash-tech-card {
        background-color: var(--glass-bg);
        -webkit-backdrop-filter: var(--glass-blur);
        backdrop-filter: var(--glass-blur);
        box-shadow: var(--glass-shadow);
    }
    .dash-panel {
        border: 1px solid var(--glass-border);
        outline: 1px solid var(--glass-edge);
        outline-offset: -1px;
        padding: var(--space) calc(var(--space) * 1.15);
    }
    .dash-stat-card,
    .dash-tech-card {
        background:
            radial-gradient(120% 90% at 100% 0%, rgb(var(--tone) / 0.13) 0%, transparent 55%),
            var(--glass-bg);
    }
    .quick-action-pill,
    .dash-meta-badge {
        background: var(--glass-bg);
        -webkit-backdrop-filter: var(--glass-blur);
        backdrop-filter: var(--glass-blur);
        border-color: var(--glass-border);
        box-shadow: 0 4px 14px -6px rgba(30, 41, 99, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }
    .dash-panel:hover {
        border-color: rgba(255, 255, 255, 0.95);
        box-shadow: 0 18px 40px -18px rgba(30, 41, 99, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.9);
    }

    /* Panel headers: one rhythm, one badge style */
    .panel-header { margin-bottom: var(--space); align-items: flex-start; }
    .panel-header .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.28rem 0.7rem;
        border-radius: 999px;
        font-size: 0.72rem !important;
        font-weight: 700;
        white-space: nowrap;
        background: rgba(255, 255, 255, 0.7) !important;
        -webkit-backdrop-filter: blur(8px);
        backdrop-filter: blur(8px);
    }
    .panel-header > a {
        white-space: nowrap;
        padding: 0.3rem 0.7rem;
        border-radius: 999px;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }
    .panel-header > a:hover { background: rgba(59, 130, 246, 0.1); transform: translateX(2px); }

    /* Tables: fit the panel at desktop widths, glass sticky header */
    .dash-table th {
        padding: 0.6rem 0.7rem;
        background: rgba(248, 250, 252, 0.85);
        -webkit-backdrop-filter: blur(8px);
        backdrop-filter: blur(8px);
    }
    .dash-table th:first-child { border-top-left-radius: 10px; }
    .dash-table th:last-child { border-top-right-radius: 10px; }
    .dash-table td { padding: 0.75rem 0.7rem; border-bottom-color: rgba(15, 23, 42, 0.06); }
    .dash-table tbody tr:hover { background-color: rgba(255, 255, 255, 0.7); }

    /* Activity feed: glass items, aligned dots */
    .activity-item-modern {
        padding: 0.65rem 0.75rem;
        margin-bottom: 0.6rem;
        border-radius: 12px;
        background: var(--glass-bg-soft);
        border: 1px solid rgba(255, 255, 255, 0.7);
        transition: transform 0.25s var(--polish-ease), background-color 0.2s ease, box-shadow 0.25s ease;
    }
    .activity-item-modern:last-child { margin-bottom: 0; padding-bottom: 0.65rem; }
    .activity-item-modern:hover {
        transform: translateX(4px);
        background: rgba(255, 255, 255, 0.85);
        box-shadow: 0 8px 20px -12px rgba(30, 41, 99, 0.25);
    }
    .activity-dot-modern { top: 0.9rem; }
    .activity-feed-modern { padding-right: 0.35rem; }

    /* Financial boxes + district cards: glass tiles, equal heights, motion */
    .fin-metric-box,
    .district-stat-card {
        height: 100%;
        min-height: 92px;
        border-radius: 14px;
        -webkit-backdrop-filter: blur(10px);
        backdrop-filter: blur(10px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
        transition: transform 0.3s var(--polish-ease), box-shadow 0.3s ease, border-color 0.2s ease;
    }
    .fin-invoiced { background: rgba(248, 250, 252, 0.65); }
    .fin-paid     { background: rgba(240, 253, 244, 0.7); }
    .fin-pending  { background: rgba(255, 251, 235, 0.7); }
    .district-stat-card { background: var(--glass-bg-soft); border-color: rgba(255, 255, 255, 0.75); }
    .fin-metric-box:hover,
    .district-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 26px -14px rgba(30, 41, 99, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.9);
    }
    .progress { overflow: hidden; box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.08); }
    .progress-bar { transition: width 1.2s var(--polish-ease); }
    .progress-bar.bg-success { background: linear-gradient(90deg, #16a34a, #22c55e) !important; }
    .progress-bar.bg-warning { background: linear-gradient(90deg, #f59e0b, #fbbf24) !important; }

    .district-stat-card { min-height: 100px; }

    /* Inline font-family:'Sora' has no fallback and drops to a serif font when Sora is missing */
    .page [style*="Sora"] {
        font-family: 'Sora', 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif !important;
        font-variant-numeric: tabular-nums;
        letter-spacing: -0.01em;
    }

    /* Narrower desktops: tighten table cells so columns fit without scrolling */
    @media (max-width: 1399.98px) {
        .dash-table th,
        .dash-table td { padding-left: 0.55rem; padding-right: 0.55rem; }
        .dash-table td .text-truncate { max-width: 140px !important; }
    }

    /* Empty states */
    .empty-state-wrap { padding: 2.5rem 1rem; }

    /* Dark glass */
    body[data-theme-version="dark"] .page {
        --glass-bg: rgba(32, 34, 44, 0.62);
        --glass-bg-soft: rgba(40, 42, 54, 0.5);
        --glass-border: rgba(255, 255, 255, 0.08);
        --glass-edge: rgba(0, 0, 0, 0.2);
        --glass-shadow: 0 10px 30px -14px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.05);
    }
    body[data-theme-version="dark"] .content-body:has(> .container-fluid > main.page) {
        background:
            radial-gradient(40rem 26rem at 8% -4%, rgba(59, 130, 246, 0.16), transparent 60%),
            radial-gradient(34rem 24rem at 96% 6%, rgba(139, 92, 246, 0.14), transparent 60%),
            radial-gradient(38rem 28rem at 60% 62%, rgba(20, 184, 166, 0.08), transparent 60%);
        background-attachment: fixed;
    }
    body[data-theme-version="dark"] .dash-stat-card,
    body[data-theme-version="dark"] .dash-tech-card {
        background:
            radial-gradient(120% 90% at 100% 0%, rgb(var(--tone) / 0.18) 0%, transparent 60%),
            var(--glass-bg);
    }
    body[data-theme-version="dark"] .dash-panel:hover { border-color: rgba(255, 255, 255, 0.14); }
    body[data-theme-version="dark"] .dash-table th { background: rgba(32, 34, 44, 0.9); }
    body[data-theme-version="dark"] .dash-table td { border-bottom-color: rgba(255, 255, 255, 0.06); }
    body[data-theme-version="dark"] .dash-table tbody tr:hover { background-color: rgba(255, 255, 255, 0.04); }
    body[data-theme-version="dark"] .panel-header .badge { background: rgba(255, 255, 255, 0.06) !important; }
    body[data-theme-version="dark"] .activity-item-modern { border-color: rgba(255, 255, 255, 0.06); }
    body[data-theme-version="dark"] .activity-item-modern:hover { background: rgba(255, 255, 255, 0.06); }
    body[data-theme-version="dark"] .district-stat-card { border-color: rgba(255, 255, 255, 0.08); }
    body[data-theme-version="dark"] .fin-metric-box,
    body[data-theme-version="dark"] .district-stat-card { box-shadow: none; }
    body[data-theme-version="dark"] .quick-action-pill,
    body[data-theme-version="dark"] .dash-meta-badge { box-shadow: none; }

    /* Browsers without backdrop-filter: fall back to near-solid surfaces */
    @supports not ((backdrop-filter: blur(1px)) or (-webkit-backdrop-filter: blur(1px))) {
        .page { --glass-bg: rgba(255, 255, 255, 0.94); --glass-bg-soft: rgba(255, 255, 255, 0.9); }
        body[data-theme-version="dark"] .page { --glass-bg: rgba(32, 34, 44, 0.96); --glass-bg-soft: rgba(40, 42, 54, 0.94); }
    }

    @media (max-width: 991.98px) {
        .page { --space: 1rem; }
    }

    @media (max-width: 575.98px) {
        .page { --space: 0.85rem; }
        .dash-panel { padding: 1rem; }
        .panel-header { flex-wrap: wrap; }
        .dash-stat-card .stat-num,
        .dash-tech-card .stat-num { font-size: 1.55rem; }
        .dash-stat-card .stat-ic-box,
        .dash-tech-card .stat-ic-box { width: 40px; height: 40px; font-size: 1.05rem; }
        .dash-stat-card .stat-info::after,
        .dash-tech-card .stat-info::after { display: none; }
    }

    @media (prefers-reduced-motion: reduce) {
        .dash-stat-card, .dash-tech-card, .dash-panel, .dash-table tbody tr {
            animation: none !important;
            opacity: 1 !important;
        }
        .dash-stat-card::after, .dash-tech-card::after { display: none; }
        .stat-card-link:hover .dash-stat-card,
        .stat-card-link:hover .dash-tech-card,
        .stat-card-link:hover .stat-ic-box { transform: none; }
    }

    @media (prefers-reduced-motion: reduce) {
        .dash-stat-card, .dash-tech-card, .quick-action-pill, .stat-ic-box, .live-dot-pulse {
            transition: none !important;
            animation: none !important;
        }
        .dash-stat-card:hover, .dash-tech-card:hover, .quick-action-pill:hover { transform: none; }
    }

    /* ==========================================================================
       FIX: quick-launch pills were clipped on hover/focus
       The bar scrolls (overflow:auto) with no top padding, so the -2px lift,
       shadow, border and focus ring were cut off and the label could shrink.
       ========================================================================== */
    .quick-launch-bar {
        flex-wrap: wrap;
        overflow: visible;
        gap: 0.55rem;
        padding: 0;
    }
    .quick-launch-bar > span:first-child {
        flex-shrink: 0;
        white-space: nowrap;
        margin-right: 0.25rem !important;
    }
    .quick-action-pill { position: relative; }
    .quick-action-pill:focus-visible {
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.35), 0 4px 12px rgba(0, 0, 0, 0.08);
    }
    @media (max-width: 575.98px) {
        /* Phones: one scrolling line, with room above and below for the lift and shadow */
        .quick-launch-bar {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: visible;
            padding: 6px 4px 10px;
            margin-left: -4px;
            margin-right: -4px;
            scrollbar-width: none;
        }
        .quick-launch-bar::-webkit-scrollbar { display: none; }
    }

    /* ==========================================================================
       SECTION REDESIGN - panel headers, row-card tables, legend, finance, districts
       ========================================================================== */
    .dash-panel { --tone: 37 99 235; }
    .panel-head-main { display: flex; align-items: center; gap: 0.8rem; min-width: 0; }
    .panel-icon {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        color: #fff;
        background: linear-gradient(135deg, rgb(var(--tone)) 0%, rgb(var(--tone) / 0.7) 100%);
        box-shadow: 0 8px 18px -8px rgb(var(--tone) / 0.6);
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .dash-panel:hover .panel-icon { transform: rotate(-6deg) scale(1.06); }
    .panel-header { padding-bottom: 0.9rem; border-bottom: 1px solid rgba(15, 23, 42, 0.06); }
    .panel-link {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: rgb(var(--tone));
        text-decoration: none;
        white-space: nowrap;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        background: rgb(var(--tone) / 0.08);
        border: 1px solid rgb(var(--tone) / 0.16);
        transition: background-color 0.2s ease, gap 0.25s ease;
    }
    .panel-link:hover { background: rgb(var(--tone) / 0.14); gap: 0.55rem; color: rgb(var(--tone)); }
    .panel-header > .panel-link:hover { transform: none; }

    /* Row-card tables: each row is its own glass card */
    .dash-table-cards { border-collapse: separate; border-spacing: 0 0.5rem; margin-top: -0.5rem; }
    .dash-table-cards thead th { border-bottom: none; background: transparent; position: static; padding-top: 0.75rem; padding-bottom: 0.25rem; }
    .dash-table-cards tbody td {
        background: rgba(255, 255, 255, 0.55);
        border-top: 1px solid rgba(255, 255, 255, 0.8);
        border-bottom: 1px solid rgba(15, 23, 42, 0.06);
        transition: background-color 0.2s ease;
    }
    .dash-table-cards tbody tr:last-child td { border-bottom: 1px solid rgba(15, 23, 42, 0.06); }
    .dash-table-cards tbody td:first-child { border-radius: 12px 0 0 12px; border-left: 1px solid rgba(15, 23, 42, 0.05); }
    .dash-table-cards tbody td:last-child { border-radius: 0 12px 12px 0; border-right: 1px solid rgba(15, 23, 42, 0.05); }
    /* Row hover: the row never moves (no transform/filter, so it cannot cover its
       neighbours). The whole card tints in the panel tone, its outline glows, a
       left accent bar slides in and the action button fills. */
    .dash-table-cards tbody tr { box-shadow: none !important; }
    .dash-table-cards tbody tr:hover { background: transparent !important; }
    .dash-table-cards tbody td {
        transition: background-color 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
    }
    .dash-table-cards tbody td:first-child { position: relative; }
    .dash-table-cards tbody td:first-child::before {
        content: '';
        position: absolute;
        left: 0;
        top: 18%;
        bottom: 18%;
        width: 3px;
        border-radius: 0 3px 3px 0;
        background: rgb(var(--tone));
        transform: scaleY(0);
        transition: transform 0.3s var(--polish-ease);
    }
    .dash-table-cards tbody tr:hover td {
        /* uniform tint (a per-cell gradient would restart in every cell and show stripes) */
        background: linear-gradient(rgb(var(--tone) / 0.06), rgb(var(--tone) / 0.06)), rgba(255, 255, 255, 0.92);
        border-top-color: rgb(var(--tone) / 0.28);
        border-bottom-color: rgb(var(--tone) / 0.28);
    }
    .dash-table-cards tbody tr:hover td:first-child { border-left-color: rgb(var(--tone) / 0.28); }
    .dash-table-cards tbody tr:hover td:last-child { border-right-color: rgb(var(--tone) / 0.28); }
    .dash-table-cards tbody tr:hover td:first-child::before { transform: scaleY(1); }
    .dash-table-cards tbody tr:hover .client-avatar {
        background: rgb(var(--tone));
        color: #fff;
        border-color: transparent;
        transform: none;
    }
    .dash-table-cards tbody tr:hover .client-name { color: rgb(var(--tone)); }
    .dash-table-cards tbody tr:hover .ref-pill { background: rgb(var(--tone)); color: #fff; border-color: transparent; }
    .dash-table-cards tbody tr:hover .chip { transform: none; }
    .dash-table-cards tbody tr:hover .btn-table-action {
        background: rgb(var(--tone));
        border-color: rgb(var(--tone));
        color: #fff;
        box-shadow: 0 6px 14px -6px rgb(var(--tone) / 0.6);
        transform: none;
    }
    .dash-table-cards tbody tr:hover .btn-table-action i { transform: translateX(2px); }
    .dash-table-cards .btn-table-action i { transition: transform 0.25s var(--polish-ease); }
    .dash-table-cards .client-avatar,
    .dash-table-cards .client-name,
    .dash-table-cards .ref-pill { transition: background-color 0.25s ease, color 0.25s ease, border-color 0.25s ease; }
    body[data-theme-version="dark"] .dash-table-cards tbody tr:hover td {
        background: linear-gradient(rgb(var(--tone) / 0.12), rgb(var(--tone) / 0.12)), rgba(32, 34, 44, 0.9);
    }
    body[data-theme-version="dark"] .dash-table-cards tbody tr:hover .client-name { color: #fff; }
    .dash-table-cards tr.empty-row td { background: transparent; border: none; }
    /* Keep both tables inside their panels: compact cells, chips and places may wrap */
    .dash-table-cards th,
    .dash-table-cards td { padding-left: 0.55rem; padding-right: 0.55rem; }
    .dash-table-cards .chip { white-space: normal; line-height: 1.25; max-width: 128px; }
    .dash-table-cards .cell-strong { white-space: normal; }
    .dash-table-cards .btn-table-action { padding: 0.28rem 0.6rem; white-space: nowrap; }
    .dash-table-cards td .text-truncate { max-width: 150px !important; }
    /* Narrower desktops: action buttons become icon-only (label kept for screen readers) */
    @media (max-width: 1399.98px) {
        .dash-table-cards .btn-table-action { padding: 0.32rem 0.45rem; }
        .dash-table-cards .btn-table-action > span {
            position: absolute; width: 1px; height: 1px; overflow: hidden;
            clip: rect(0 0 0 0); clip-path: inset(50%); white-space: nowrap;
        }
        .dash-table-cards td .text-truncate { max-width: 130px !important; }
    }

    .min-w-0 { min-width: 0; }
    .client-name { font-weight: 600; color: var(--navy-950); font-size: 0.83rem; line-height: 1.3; }
    .cell-meta { font-size: 0.7rem; color: var(--ink-500); margin-top: 0.15rem; }
    .cell-meta i { opacity: 0.8; }
    .cell-strong { font-weight: 500; color: var(--ink-700); white-space: nowrap; }
    .cell-ic { color: var(--ink-500); margin-right: 0.3rem; font-size: 0.8rem; }
    /* Plan type was light grey on white (too low contrast) */
    .plan-type { font-size: 0.76rem; color: var(--ink-700); font-weight: 500; }
    .mineral-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 600;
        color: #92400e;
        background: rgba(212, 175, 55, 0.14);
        border: 1px solid rgba(212, 175, 55, 0.35);
        white-space: nowrap;
    }
    .mineral-tag::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #d4af37; }
    .ref-pill {
        display: inline-block;
        font-family: ui-monospace, 'SFMono-Regular', Consolas, monospace;
        font-size: 0.74rem;
        font-weight: 700;
        color: rgb(var(--tone));
        background: rgb(var(--tone) / 0.08);
        border: 1px solid rgb(var(--tone) / 0.18);
        padding: 0.15rem 0.5rem;
        border-radius: 6px;
        white-space: nowrap;
    }
    .survey-badge { gap: 0.3rem; border-radius: 999px; padding: 0.22rem 0.6rem; }
    .chip { white-space: nowrap; }

    /* Donut + custom legend */
    .chart-container-rel { min-height: 210px; }
    .chart-donut-center { top: 50%; }
    .breakdown-legend { list-style: none; margin: 0.75rem 0 0; padding: 0; display: grid; gap: 0.45rem; }
    .breakdown-legend li {
        display: grid;
        grid-template-columns: 10px 1fr auto 2.6rem;
        grid-template-rows: auto 4px;
        align-items: center;
        column-gap: 0.55rem;
        row-gap: 0.3rem;
        padding: 0.45rem 0.6rem;
        border-radius: 10px;
        transition: background-color 0.2s ease;
    }
    .breakdown-legend li:hover { background: rgba(255, 255, 255, 0.7); }
    .lg-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--c); box-shadow: 0 0 0 3px color-mix(in srgb, var(--c) 18%, transparent); }
    .lg-label { font-size: 0.78rem; color: var(--ink-700); font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .lg-val { font-size: 0.8rem; font-weight: 700; color: var(--navy-950); font-variant-numeric: tabular-nums; }
    .lg-pct { font-size: 0.7rem; color: var(--ink-500); text-align: right; font-variant-numeric: tabular-nums; }
    .lg-bar { grid-column: 2 / -1; height: 4px; border-radius: 4px; background: rgba(15, 23, 42, 0.06); overflow: hidden; }
    .lg-bar > span { display: block; height: 100%; width: var(--p); background: var(--c); border-radius: 4px; animation: barGrow 1.1s var(--polish-ease) 0.6s both; transform-origin: left; }
    @keyframes barGrow { from { transform: scaleX(0); } to { transform: scaleX(1); } }

    /* Activity */
    .live-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.28rem 0.7rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #15803d;
        background: rgba(34, 197, 94, 0.1);
        border: 1px solid rgba(34, 197, 94, 0.25);
        white-space: nowrap;
    }
    .activity-text { font-size: 0.82rem; font-weight: 600; color: var(--navy-950); line-height: 1.35; margin-bottom: 0.2rem; }
    .activity-item-modern .chip { font-size: 0.68rem; padding: 0.12rem 0.55rem; }
    .activity-feed-modern:has(.empty-state-wrap)::before { display: none; }
    .activity-feed-modern:has(.empty-state-wrap) { padding-left: 0; }

    /* Empty states */
    .empty-state-wrap { text-align: center; padding: 2.25rem 1rem; }
    .empty-ic {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: rgb(var(--tone));
        background: rgb(var(--tone) / 0.1);
        border: 1px solid rgb(var(--tone) / 0.2);
        margin-bottom: 0.75rem;
        animation: emptyFloat 3.5s ease-in-out infinite;
    }
    @keyframes emptyFloat { 0%, 100% { translate: 0 0; } 50% { translate: 0 -5px; } }
    .empty-title { font-weight: 700; color: var(--navy-950); font-size: 0.9rem; }
    .empty-hint { font-size: 0.76rem; color: var(--ink-500); max-width: 260px; margin: 0.25rem auto 0; }

    /* Finance */
    .recovery-block { margin-bottom: var(--space); }
    .recovery-labels { display: flex; justify-content: space-between; gap: 1rem; font-size: 0.76rem; color: var(--ink-500); margin-bottom: 0.45rem; }
    .recovery-labels strong { color: var(--navy-950); }
    .rl-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 0.3rem; }
    .rl-paid { background: #22c55e; }
    .rl-pending { background: #f59e0b; }
    .recovery-bar { height: 12px; border-radius: 999px; background: rgba(245, 158, 11, 0.15); gap: 3px; }
    .recovery-bar .progress-bar { border-radius: 999px; }
    .fin-metric-box { padding: 1rem; display: flex; flex-direction: column; gap: 0.15rem; }
    .fin-ic {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        margin-bottom: 0.45rem;
        color: #fff;
    }
    .fin-invoiced .fin-ic { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
    .fin-paid .fin-ic { background: linear-gradient(135deg, #16a34a, #4ade80); }
    .fin-pending .fin-ic { background: linear-gradient(135deg, #d97706, #fbbf24); }
    .fin-label { font-size: 0.74rem; font-weight: 600; color: var(--ink-500); }
    .fin-value { font-family: 'Sora', 'Inter', system-ui, sans-serif; font-weight: 700; font-size: 1.15rem; color: var(--navy-950); font-variant-numeric: tabular-nums; letter-spacing: -0.01em; }
    .fin-paid .fin-value { color: #15803d; }
    .fin-pending .fin-value { color: #b45309; }
    .fin-caption { font-size: 0.7rem; color: var(--ink-500); }

    /* Districts */
    .district-stat-card {
        text-align: left;
        align-items: stretch;
        justify-content: flex-start;
        gap: 0.1rem;
        padding: 0.85rem 0.9rem 0.8rem;
        min-height: 0;
    }
    .ds-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.3rem; }
    .ds-top i { color: rgb(var(--c)); font-size: 0.95rem; }
    .ds-num { font-family: 'Sora', 'Inter', system-ui, sans-serif; font-weight: 700; font-size: 1.35rem; color: rgb(var(--c)); line-height: 1; }
    .ds-name { font-size: 0.8rem; font-weight: 700; color: var(--navy-950); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ds-caption { font-size: 0.68rem; color: var(--ink-500); }
    .ds-bar { display: block; height: 4px; margin-top: 0.55rem; border-radius: 4px; background: rgb(var(--c) / 0.12); overflow: hidden; }
    .ds-bar > span { display: block; height: 100%; width: max(var(--p), 6%); background: rgb(var(--c)); border-radius: 4px; transform-origin: left; animation: barGrow 1.1s var(--polish-ease) 0.7s both; }
    .ds-empty { text-align: center; justify-content: center; color: var(--ink-500); }

    /* Dark mode for the redesigned sections */
    body[data-theme-version="dark"] .panel-header { border-bottom-color: rgba(255, 255, 255, 0.06); }
    body[data-theme-version="dark"] .dash-table-cards tbody td { background: rgba(255, 255, 255, 0.03); border-color: rgba(255, 255, 255, 0.05); }
    body[data-theme-version="dark"] .dash-table-cards tbody tr:hover td { background: rgba(255, 255, 255, 0.07); }
    body[data-theme-version="dark"] .client-name,
    body[data-theme-version="dark"] .activity-text,
    body[data-theme-version="dark"] .lg-val,
    body[data-theme-version="dark"] .empty-title,
    body[data-theme-version="dark"] .fin-value,
    body[data-theme-version="dark"] .ds-name,
    body[data-theme-version="dark"] .recovery-labels strong { color: #f1f5f9; }
    body[data-theme-version="dark"] .plan-type,
    body[data-theme-version="dark"] .cell-strong,
    body[data-theme-version="dark"] .lg-label { color: #cbd5e1; }
    body[data-theme-version="dark"] .mineral-tag { color: #fcd34d; background: rgba(212, 175, 55, 0.12); border-color: rgba(212, 175, 55, 0.3); }
    body[data-theme-version="dark"] .fin-paid .fin-value { color: #4ade80; }
    body[data-theme-version="dark"] .fin-pending .fin-value { color: #fbbf24; }
    body[data-theme-version="dark"] .breakdown-legend li:hover { background: rgba(255, 255, 255, 0.05); }
    body[data-theme-version="dark"] .lg-bar { background: rgba(255, 255, 255, 0.08); }
    body[data-theme-version="dark"] .live-badge { color: #4ade80; background: rgba(34, 197, 94, 0.12); }
    body[data-theme-version="dark"] .ds-num,
    body[data-theme-version="dark"] .ds-top i { filter: brightness(1.5); }

    @media (max-width: 575.98px) {
        .panel-icon { width: 34px; height: 34px; border-radius: 10px; font-size: 0.9rem; }
        .panel-header .panel-link,
        .panel-header .panel-total-badge,
        .panel-header .live-badge,
        .panel-header > .chip { margin-left: auto; }
    }
    @media (prefers-reduced-motion: reduce) {
        .lg-bar > span, .ds-bar > span, .empty-ic { animation: none !important; }
        .dash-table-cards tbody tr:hover { transform: none; }
    }
</style>

<div class="content-body default-height">
    <div class="container-fluid">
        <main class="page">
            
            <!-- Modern Dynamic Welcome Header -->
            <div class="dash-head">
                <div>
                    <span class="dash-eyebrow">
                        <i class="bi bi-shield-check"></i> {{ $regionName }}
                    </span>
                    <h1 class="dash-title">
                        {{ $greeting }}, {{ Auth::user()->name ?? 'Officer' }} 👋
                    </h1>
                    <p class="dash-subtitle">
                        @if($pendingCount > 0)
                            <span class="fw-semibold text-warning-emphasis">{{ $pendingCount }}</span> {{ \Illuminate\Support\Str::plural('application', $pendingCount) }} awaiting statutory action across {{ max(1, $districtStats->count()) }} {{ \Illuminate\Support\Str::plural('district', max(1, $districtStats->count())) }}.
                        @else
                            All applications and technical geospatial surveys are operational across {{ max(1, $districtStats->count()) }} {{ \Illuminate\Support\Str::plural('district', max(1, $districtStats->count())) }}.
                        @endif
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="dash-meta-badge">
                        <span class="live-dot-pulse"></span>
                        <span class="fw-semibold text-dark">System Online</span>
                        <span class="text-muted">|</span>
                        <i class="bi bi-calendar3 text-muted"></i>
                        <span>{{ date('D, d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Workflow Ribbon -->
            <div class="quick-launch-bar">
                <span class="small fw-bold text-muted text-uppercase me-2" style="font-size: 0.7rem; letter-spacing: 0.06em;">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Launch:
                </span>
                @can('application.create')
                <a href="{{ route('step1') }}" class="quick-action-pill pill-lease">
                    <i class="bi bi-file-earmark-plus text-primary"></i>
                    <span>Lease Application</span>
                </a>
                @endcan
                @can('dgps.create')
                <a href="{{ route('dgps-survey.step', ['step' => 1]) }}" class="quick-action-pill pill-dgps">
                    <i class="bi bi-pin-map-fill" style="color: #0284c7;"></i>
                    <span>DGPS Survey</span>
                </a>
                @endcan
                @can('drone.create')
                <a href="{{ route('drone-survey.step', ['step' => 1]) }}" class="quick-action-pill pill-drone">
                    <i class="bi bi-airplane-fill" style="color: #7c3aed;"></i>
                    <span>Drone Survey</span>
                </a>
                @endcan
                @can('environment.b2.create')
                <a href="{{ route('eviron.create') }}" class="quick-action-pill pill-env">
                    <i class="bi bi-tree-fill text-success"></i>
                    <span>Env Clearance</span>
                </a>
                @endcan
                @can('ec_compliance.create')
                <a href="{{ route('ec-compliance.step', ['step' => 1]) }}" class="quick-action-pill pill-ec">
                    <i class="bi bi-clipboard2-check text-warning"></i>
                    <span>EC Compliance</span>
                </a>
                @endcan
            </div>

            <!-- Primary KPI Stat Cards (Lease & Mining Applications) -->
            <div class="row g-3 mb-3">
                <div class="col-6 col-lg-3">
                    <a href="{{ route('application.index') }}" class="stat-card-link" title="View Active Applications">
                        <div class="dash-stat-card">
                            <div class="stat-ic-box" style="background: rgba(59, 130, 246, 0.12); color: #2563eb;">
                                <i class="bi bi-folder2-open"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $activeCount }}</div>
                                <div class="stat-label">Active Applications</div>
                                <div class="stat-trend text-success"><i class="bi bi-check2-circle"></i> Operational</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('application.index') }}" class="stat-card-link" title="View Pending Applications">
                        <div class="dash-stat-card">
                            <div class="stat-ic-box" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $pendingCount }}</div>
                                <div class="stat-label">Pending Validation</div>
                                <div class="stat-trend text-warning-emphasis"><i class="bi bi-clock"></i> Review Needed</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('application.index') }}" class="stat-card-link" title="View Approved Applications">
                        <div class="dash-stat-card">
                            <div class="stat-ic-box" style="background: rgba(34, 197, 94, 0.12); color: #16a34a;">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $approvedCount }}</div>
                                <div class="stat-label">Approved &amp; Verified</div>
                                <div class="stat-trend text-success"><i class="bi bi-shield-check"></i> All-Time Safe</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('application.index') }}" class="stat-card-link" title="View Archived Applications">
                        <div class="dash-stat-card">
                            <div class="stat-ic-box" style="background: rgba(148, 163, 184, 0.15); color: #64748b;">
                                <i class="bi bi-archive-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $archivedCount }}</div>
                                <div class="stat-label">Archived Files</div>
                                <div class="stat-trend text-muted"><i class="bi bi-lock-fill"></i> Secured</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Technical Operations & Surveys Execution Matrix -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <a href="{{ route('dgps-survey.index') }}" class="stat-card-link" title="View DGPS Surveys">
                        <div class="dash-tech-card card-dgps">
                            <div class="stat-ic-box" style="background: rgba(2, 132, 199, 0.12); color: #0284c7;">
                                <i class="bi bi-pin-map-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $dgpsTotal }}</div>
                                <div class="stat-label">DGPS Demarcation</div>
                                <div class="stat-trend text-success"><i class="bi bi-check2-circle"></i> {{ $dgpsCompleted }} Completed</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('drone-survey.index') }}" class="stat-card-link" title="View Drone Surveys">
                        <div class="dash-tech-card card-drone">
                            <div class="stat-ic-box" style="background: rgba(124, 58, 237, 0.12); color: #7c3aed;">
                                <i class="bi bi-airplane-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $droneTotal }}</div>
                                <div class="stat-label">Drone Aerial Topo</div>
                                <div class="stat-trend" style="color:#7c3aed;"><i class="bi bi-folder-check"></i> {{ $droneReady }} Reports Ready</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('eviron.index') }}" class="stat-card-link" title="View Environmental Projects">
                        <div class="dash-tech-card card-env">
                            <div class="stat-ic-box" style="background: rgba(22, 163, 74, 0.12); color: #16a34a;">
                                <i class="bi bi-tree-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $envProjectsTotal }}</div>
                                <div class="stat-label">Env Clearances</div>
                                <div class="stat-trend text-success"><i class="bi bi-shield-check"></i> B1 / B2 Active</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('ec-compliance.index') }}" class="stat-card-link" title="View EC Compliances">
                        <div class="dash-tech-card card-ec">
                            <div class="stat-ic-box" style="background: rgba(217, 119, 6, 0.12); color: #d97706;">
                                <i class="bi bi-clipboard-data-fill"></i>
                            </div>
                            <div class="stat-info">
                                <div class="stat-num">{{ $ecComplianceTotal }}</div>
                                <div class="stat-label">EC Compliance</div>
                                <div class="stat-trend text-warning-emphasis"><i class="bi bi-clock-history"></i> Half-Yearly Cycles</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            @php
                // Shared palette for the donut and its custom legend (same order as the chart dataset)
                $breakdownColors = ['#0284C7', '#7C3AED', '#16A34A', '#0B2B5E', '#D4AF37'];
                $breakdownTotal = array_sum($chartData['data']);
                // Scale for the district bars: largest figure shown in the panel
                $districtMax = max(1, $dgpsTotal, $droneTotal, (int) ($districtStats->max('total') ?? 0));
            @endphp

            <!-- Row 1: Recent Applications & Operations Breakdown Chart -->
            <div class="row g-3 mb-4">
                <!-- Recent applications -->
                <div class="col-lg-8">
                    <div class="dash-panel d-flex flex-column" style="--tone: 37 99 235;">
                        <div class="panel-header">
                            <div class="panel-head-main">
                                <span class="panel-icon"><i class="bi bi-file-earmark-text"></i></span>
                                <div>
                                    <h2 class="panel-title">Recent Applications</h2>
                                    <p class="panel-subtitle">Mining lease and quarry permit pipeline</p>
                                </div>
                            </div>
                            <a href="{{ route('application.index') }}" class="panel-link">
                                View all ({{ $approvedCount + $activeCount }}) <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        <div class="table-responsive flex-grow-1">
                            <table class="dash-table dash-table-cards align-middle">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>District</th>
                                        <th>Mineral</th>
                                        <th>Plan Type</th>
                                        <th>Stage</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentApplications as $app)
                                    @php
                                        $words = explode(' ', trim($app->client));
                                        $initials = '';
                                        foreach(array_slice($words, 0, 2) as $w) {
                                            $initials .= strtoupper(substr($w, 0, 1));
                                        }
                                        if(empty($initials)) $initials = 'LA';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="client-cell">
                                                <div class="client-avatar">{{ $initials }}</div>
                                                <div class="min-w-0">
                                                    <span class="client-name">{{ $app->client }}</span>
                                                    @if(isset($app->id))
                                                        <div class="cell-meta">Ref #LA-{{ str_pad($app->id, 4, '0', STR_PAD_LEFT) }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="cell-strong"><i class="bi bi-geo-alt cell-ic"></i>{{ $app->district }}</span></td>
                                        <td><span class="mineral-tag">{{ $app->mineral }}</span></td>
                                        <td><span class="plan-type">{{ $app->plan_type }}</span></td>
                                        <td>
                                            <span class="chip chip-{{ $app->status_class }}">
                                                <i class="bi bi-{{ $app->icon }}"></i> {{ $app->stage }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ isset($app->id) ? route('viewapplication', ['id' => $app->id]) : route('application.index') }}" class="btn-table-action">
                                                <span>Open</span>
                                                <i class="bi bi-arrow-right-short"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="empty-row">
                                        <td colspan="6">
                                            <div class="empty-state-wrap">
                                                <span class="empty-ic"><i class="bi bi-inbox"></i></span>
                                                <div class="empty-title">No recent applications</div>
                                                <div class="empty-hint">New lease applications will appear here.</div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Applications Breakdown Chart -->
                <div class="col-lg-4">
                    <div class="dash-panel d-flex flex-column" style="--tone: 124 58 237;">
                        <div class="panel-header">
                            <div class="panel-head-main">
                                <span class="panel-icon"><i class="bi bi-pie-chart"></i></span>
                                <div>
                                    <h2 class="panel-title">Operations Breakdown</h2>
                                    <p class="panel-subtitle">Statutory project distribution</p>
                                </div>
                            </div>
                            <span class="panel-total-badge">{{ $breakdownTotal }} Total</span>
                        </div>
                        <div class="chart-container-rel">
                            <canvas id="appBreakdownChart"></canvas>
                            <div class="chart-donut-center">
                                <div class="chart-center-val">{{ $breakdownTotal }}</div>
                                <div class="chart-center-lbl">Operations</div>
                            </div>
                        </div>
                        <ul class="breakdown-legend">
                            @foreach($chartData['labels'] as $i => $label)
                                @php
                                    $val = (int) ($chartData['data'][$i] ?? 0);
                                    $pct = $breakdownTotal > 0 ? round($val / $breakdownTotal * 100) : 0;
                                @endphp
                                <li style="--c: {{ $breakdownColors[$i] ?? '#94a3b8' }}; --p: {{ $pct }}%;">
                                    <span class="lg-dot"></span>
                                    <span class="lg-label">{{ $label }}</span>
                                    <span class="lg-val">{{ $val }}</span>
                                    <span class="lg-pct">{{ $pct }}%</span>
                                    <span class="lg-bar"><span></span></span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Row 2: Recent Field Surveys & Live Activity Stream -->
            <div class="row g-3 mb-4">
                <!-- Recent Field Surveys (DGPS & Drone) -->
                <div class="col-lg-7">
                    <div class="dash-panel d-flex flex-column" style="--tone: 2 132 199;">
                        <div class="panel-header">
                            <div class="panel-head-main">
                                <span class="panel-icon"><i class="bi bi-geo-alt"></i></span>
                                <div>
                                    <h2 class="panel-title">Recent Field &amp; Geospatial Surveys</h2>
                                    <p class="panel-subtitle">Latest DGPS demarcations &amp; aerial photogrammetry</p>
                                </div>
                            </div>
                            <a href="{{ route('dgps-survey.index') }}" class="panel-link">
                                All Surveys <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                        <div class="table-responsive flex-grow-1">
                            <table class="dash-table dash-table-cards align-middle">
                                <thead>
                                    <tr>
                                        <th>Survey Ref</th>
                                        <th>Client / Location</th>
                                        <th>Service</th>
                                        <th>Status</th>
                                        <th class="text-end">View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentSurveys as $survey)
                                    <tr>
                                        <td>
                                            <span class="ref-pill">{{ $survey->survey_no }}</span>
                                            <div class="cell-meta"><i class="bi bi-calendar3"></i> {{ $survey->date }}</div>
                                        </td>
                                        <td>
                                            <div class="client-name text-truncate" style="max-width: 170px;">{{ $survey->client }}</div>
                                            <div class="cell-meta text-truncate" style="max-width: 170px;"><i class="bi bi-geo-alt"></i> {{ $survey->location }}</div>
                                        </td>
                                        <td>
                                            @if($survey->type === 'DGPS Demarcation')
                                                <span class="survey-badge survey-badge-dgps"><i class="bi bi-pin-map"></i> DGPS</span>
                                            @else
                                                <span class="survey-badge survey-badge-drone"><i class="bi bi-airplane"></i> Drone</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="chip chip-{{ $survey->status_class }}">
                                                <i class="bi {{ $survey->status_class === 'ok' ? 'bi-check2' : 'bi-clock' }}"></i>
                                                {{ $survey->status }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ $survey->url }}" class="btn-table-action">
                                                <span>Inspect</span>
                                                <i class="bi bi-box-arrow-up-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="empty-row">
                                        <td colspan="5">
                                            <div class="empty-state-wrap">
                                                <span class="empty-ic"><i class="bi bi-geo-alt"></i></span>
                                                <div class="empty-title">No active field surveys</div>
                                                <div class="empty-hint">Demarcations and drone flights will appear here as field missions start.</div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Live System Audit & Activity Log -->
                <div class="col-lg-5">
                    <div class="dash-panel d-flex flex-column" style="--tone: 22 163 74;">
                        <div class="panel-header">
                            <div class="panel-head-main">
                                <span class="panel-icon"><i class="bi bi-activity"></i></span>
                                <div>
                                    <h2 class="panel-title">System Audit &amp; Activity Trail</h2>
                                    <p class="panel-subtitle">Real-time actions &amp; officer verification</p>
                                </div>
                            </div>
                            <span class="live-badge"><span class="live-dot-pulse"></span> Live Log</span>
                        </div>
                        <div class="activity-feed-modern flex-grow-1 overflow-y-auto pe-1" style="max-height: 330px;">
                            @forelse($recentActivities as $act)
                            <div class="activity-item-modern">
                                <span class="activity-dot-modern {{ str_contains($act->badge_class, 'ok') ? 'dot-ok' : (str_contains($act->badge_class, 'danger') ? 'dot-danger' : 'dot-warn') }}"></span>
                                <div class="d-flex justify-content-between align-items-baseline gap-2 mb-1">
                                    <span class="chip {{ $act->badge_class }}">{{ $act->action }}</span>
                                    <span class="cell-meta text-nowrap">{{ $act->time_ago }}</span>
                                </div>
                                <div class="activity-text">{{ $act->description }}</div>
                                <div class="cell-meta d-flex align-items-center gap-1">
                                    <i class="bi bi-person-circle"></i> {{ $act->user }}
                                </div>
                            </div>
                            @empty
                            <div class="empty-state-wrap h-100 d-flex flex-column align-items-center justify-content-center">
                                <span class="empty-ic"><i class="bi bi-activity"></i></span>
                                <div class="empty-title">No recent activity</div>
                                <div class="empty-hint">Approvals, uploads and status changes will be logged here.</div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 3: Financial Revenue Health & District Geospatial Snapshot -->
            <div class="row g-3 mb-2">
                <!-- Financial Collection Health -->
                <div class="col-12 col-lg-7">
                    <div class="dash-panel d-flex flex-column" style="--tone: 217 119 6;">
                        <div class="panel-header">
                            <div class="panel-head-main">
                                <span class="panel-icon"><i class="bi bi-currency-rupee"></i></span>
                                <div>
                                    <h2 class="panel-title">Financial Revenue &amp; Recovery Health</h2>
                                    <p class="panel-subtitle">Statutory fee collection &amp; outstanding balances</p>
                                </div>
                            </div>
                            <span class="chip {{ $recoveryRate >= 50 ? 'chip-ok' : 'chip-warn' }}"><i class="bi bi-shield-check"></i> {{ $recoveryRate }}% Collected</span>
                        </div>

                        <!-- Progress Recovery Bar -->
                        <div class="recovery-block">
                            <div class="recovery-labels">
                                <span><span class="rl-dot rl-paid"></span> Realized <strong>{{ $recoveryRate }}%</strong></span>
                                <span><span class="rl-dot rl-pending"></span> Outstanding <strong>{{ 100 - $recoveryRate }}%</strong></span>
                            </div>
                            <div class="progress recovery-bar" role="group" aria-label="Collection progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $recoveryRate }}%" aria-valuenow="{{ $recoveryRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ 100 - $recoveryRate }}%" aria-valuenow="{{ 100 - $recoveryRate }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="row g-3 mt-auto">
                            <div class="col-sm-4">
                                <div class="fin-metric-box fin-invoiced">
                                    <span class="fin-ic"><i class="bi bi-receipt"></i></span>
                                    <div class="fin-label">Total Invoiced</div>
                                    <div class="fin-value">₹{{ number_format($totalBilled, 2) }}</div>
                                    <div class="fin-caption">Combined ledger</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="fin-metric-box fin-paid">
                                    <span class="fin-ic"><i class="bi bi-cash-stack"></i></span>
                                    <div class="fin-label">Paid Realized</div>
                                    <div class="fin-value">₹{{ number_format($totalPaid, 2) }}</div>
                                    <div class="fin-caption">Verified payments</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="fin-metric-box fin-pending">
                                    <span class="fin-ic"><i class="bi bi-hourglass-split"></i></span>
                                    <div class="fin-label">Pending Dues</div>
                                    <div class="fin-value">₹{{ number_format($totalPending, 2) }}</div>
                                    <div class="fin-caption">Awaiting collection</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- District Footprint Snapshot -->
                <div class="col-12 col-lg-5">
                    <div class="dash-panel d-flex flex-column" style="--tone: 11 43 94;">
                        <div class="panel-header">
                            <div class="panel-head-main">
                                <span class="panel-icon"><i class="bi bi-map"></i></span>
                                <div>
                                    <h2 class="panel-title">Jurisdiction District Footprint</h2>
                                    <p class="panel-subtitle">Field operations &amp; site distribution</p>
                                </div>
                            </div>
                            <span class="panel-total-badge">
                                {{ $districtStats->count() }} Active {{ \Illuminate\Support\Str::plural('Hub', $districtStats->count()) }}
                            </span>
                        </div>
                        <div class="row g-3">
                            @forelse($districtStats as $stat)
                            <div class="col-6 col-sm-4">
                                <div class="district-stat-card" style="--c: 11 43 94; --p: {{ round(((int) $stat->total) / $districtMax * 100) }}%;">
                                    <div class="ds-top"><i class="bi bi-geo-alt-fill"></i><span class="ds-num">{{ $stat->total }}</span></div>
                                    <div class="ds-name">{{ $stat->name }}</div>
                                    <div class="ds-caption">Mining &amp; Leases</div>
                                    <span class="ds-bar"><span></span></span>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="district-stat-card ds-empty">No district data available</div>
                            </div>
                            @endforelse
                            <div class="col-6 col-sm-4">
                                <div class="district-stat-card" style="--c: 2 132 199; --p: {{ round($dgpsTotal / $districtMax * 100) }}%;">
                                    <div class="ds-top"><i class="bi bi-pin-map-fill"></i><span class="ds-num">{{ $dgpsTotal }}</span></div>
                                    <div class="ds-name">All Districts</div>
                                    <div class="ds-caption">DGPS Surveys</div>
                                    <span class="ds-bar"><span></span></span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="district-stat-card" style="--c: 124 58 237; --p: {{ round($droneTotal / $districtMax * 100) }}%;">
                                    <div class="ds-top"><i class="bi bi-airplane-fill"></i><span class="ds-num">{{ $droneTotal }}</span></div>
                                    <div class="ds-name">All Districts</div>
                                    <div class="ds-caption">Drone Surveys</div>
                                    <span class="ds-bar"><span></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Count-up for KPI numbers and the donut centre value (visual only; final text is the server value)
document.addEventListener('DOMContentLoaded', function() {
    if (reduceMotion) return;
    document.querySelectorAll('.dash-stat-card .stat-num, .dash-tech-card .stat-num, .chart-center-val').forEach(function(el, i) {
        var target = parseInt((el.textContent || '').replace(/[^0-9]/g, ''), 10);
        if (!target || target < 1) return;
        var original = el.textContent;
        var start = null;
        var duration = 900 + Math.min(target, 50) * 6;
        var delay = 120 + i * 60;
        el.textContent = '0';
        setTimeout(function() {
            requestAnimationFrame(function step(ts) {
                if (!start) start = ts;
                var p = Math.min((ts - start) / duration, 1);
                var eased = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(target * eased);
                if (p < 1) { requestAnimationFrame(step); } else { el.textContent = original; }
            });
        }, delay);
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var canvasEl = document.getElementById('appBreakdownChart');
    if (!canvasEl) return;
    
    var ctx = canvasEl.getContext('2d');
    var chartData = {!! json_encode($chartData) !!};
    
    // Theme awareness
    var isDark = document.body.getAttribute('data-theme-version') === 'dark';
    var sliceBorderColor = isDark ? '#202020' : '#ffffff';
    var legendTextColor = isDark ? '#cbd5e1' : '#475569';
    
    var breakdownChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: chartData.labels,
            datasets: [{
                data: chartData.data,
                backgroundColor: [
                    '#0284C7', // Sky Blue for DGPS
                    '#7C3AED', // Purple for Drone
                    '#16A34A', // Green for Environment
                    '#0B2B5E', // Navy for Lease
                    '#D4AF37'  // Gold for Mining
                ],
                borderWidth: 3,
                borderColor: sliceBorderColor,
                borderRadius: 6,
                spacing: 2,
                hoverOffset: 12,
                hoverBorderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: 8 },
            animation: reduceMotion ? false : {
                animateRotate: true,
                animateScale: true,
                duration: 1200,
                easing: 'easeOutQuart'
            },
            plugins: {
                legend: {
                    display: false, // custom legend with values and percentages is rendered in Blade
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 8,
                        padding: 12,
                        color: legendTextColor,
                        font: { size: 11, family: "'Inter', sans-serif" }
                    }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#f8fafc',
                    bodyColor: '#cbd5e1',
                    padding: 10,
                    cornerRadius: 10,
                    boxPadding: 4,
                    usePointStyle: true,
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.raw + ' records';
                        }
                    }
                }
            },
            cutout: '72%'
        }
    });

    // Observer to re-tune chart border and legend on theme toggle
    var themeObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'data-theme-version') {
                var currentDark = document.body.getAttribute('data-theme-version') === 'dark';
                breakdownChart.data.datasets[0].borderColor = currentDark ? '#202020' : '#ffffff';
                breakdownChart.options.plugins.legend.labels.color = currentDark ? '#cbd5e1' : '#475569';
                breakdownChart.update();
            }
        });
    });
    themeObserver.observe(document.body, { attributes: true, attributeFilter: ['data-theme-version'] });
});
</script>

@endsection

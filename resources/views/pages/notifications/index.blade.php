@extends('layouts.app')

@section('title', 'Notifications & Alerts • GTMS')

@section('main_content')
<style>
    /* ==========================================================================
       GTMS NOTIFICATION CENTER — UI-UX-PRO-MAX ENTERPRISE SPECIFICATION
       ========================================================================== */

    .notif-container {
        padding-bottom: 2.5rem;
    }

    /* Page Header & Badges */
    .page-titles {
        margin-bottom: 1.25rem !important;
    }
    .header-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #EFF6FF;
        color: #1E3A8A;
        border: 1.5px solid #BFDBFE;
        padding: 4px 10px;
        border-radius: 9999px;
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }
    .live-pulse-dot {
        width: 7px;
        height: 7px;
        background-color: #10B981;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseRing 1.8s infinite cubic-bezier(0.66, 0, 0, 1);
    }
    @keyframes pulseRing {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    /* Bento-Style KPI Metric Cards */
    .card-kpi-pro {
        border-radius: 12px;
        border: 1.5px solid #CBD5E1;
        background: #FFFFFF;
        padding: 16px 18px !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .card-kpi-pro:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.08);
        border-color: #94A3B8;
    }
    .card-kpi-pro::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3.5px;
    }
    .kpi-border-blue::before { background: linear-gradient(90deg, #1D4ED8, #3B82F6); }
    .kpi-border-amber::before { background: linear-gradient(90deg, #D97706, #F59E0B); }
    .kpi-border-emerald::before { background: linear-gradient(90deg, #059669, #10B981); }
    .kpi-border-purple::before { background: linear-gradient(90deg, #7C3AED, #8B5CF6); }

    .kpi-eyebrow {
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        font-weight: 800;
        color: #1E293B;
        margin-bottom: 2px;
        text-transform: uppercase;
        line-height: 1.2;
    }
    .kpi-value {
        font-size: 1.55rem;
        font-weight: 900;
        line-height: 1.2;
        letter-spacing: -0.02em;
        margin-bottom: 0;
        color: #0F172A;
    }
    .kpi-subtext {
        font-size: 0.76rem;
        color: #475569;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
        font-weight: 500;
    }
    .kpi-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }

    /* Filter & Controls Toolbar */
    .filter-toolbar-card {
        background: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        border-radius: 12px;
        padding: 12px 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        margin-bottom: 1.25rem;
    }
    .nav-pills-custom .nav-link {
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #475569;
        padding: 6px 14px;
        border: 1px solid transparent;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .nav-pills-custom .nav-link:hover {
        background-color: #F1F5F9;
        color: #0F172A;
    }
    .nav-pills-custom .nav-link.active {
        background-color: #0F1E4D !important;
        color: #FFFFFF !important;
        box-shadow: 0 2px 6px rgba(15, 30, 77, 0.2);
    }
    .nav-pills-custom .nav-link.active .badge-count {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: #FFFFFF !important;
    }
    .badge-count {
        font-size: 0.70rem;
        padding: 2px 6px;
        border-radius: 9999px;
        background-color: #E2E8F0;
        color: #334155;
        font-weight: 700;
    }

    /* Search & Category Filter Elements */
    .search-input-wrap {
        position: relative;
        min-width: 240px;
    }
    .search-input-wrap i {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        font-size: 0.86rem;
    }
    .search-input-wrap .form-control {
        height: 38px;
        padding-left: 32px;
        font-size: 0.83rem;
        border-radius: 8px;
        border: 1.5px solid #CBD5E1;
        background: #F8FAFC;
        font-weight: 500;
    }
    .search-input-wrap .form-control:focus {
        background: #FFFFFF;
        border-color: #0F1E4D;
        box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.08);
    }

    /* Notification Feed List */
    .notif-card-group {
        border: 1.5px solid #E2E8F0;
        border-radius: 12px;
        background: #FFFFFF;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }
    .notif-row {
        position: relative;
        padding: 16px 20px;
        border-bottom: 1px solid #F1F5F9;
        transition: all 0.18s ease;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        text-decoration: none !important;
    }
    .notif-row:last-child {
        border-bottom: none;
    }
    .notif-row:hover {
        background-color: #F8FAFC;
    }
    .notif-row.is-unread {
        background-color: #F8FBFF;
        border-left: 4px solid #2563EB;
    }
    .notif-row.is-unread:hover {
        background-color: #F0F7FF;
    }

    /* Notification Visual Elements */
    .notif-avatar {
        width: 44px;
        height: 44px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .notif-content-area {
        flex-grow: 1;
        min-width: 0;
    }
    .notif-header-line {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 4px;
    }
    .notif-item-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: #0F172A;
        line-height: 1.3;
        margin-bottom: 0;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .notif-body-text {
        font-size: 0.84rem;
        color: #475569;
        line-height: 1.45;
        margin-bottom: 8px;
        font-weight: 450;
    }
    .notif-meta-strip {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 0.74rem;
    }
    .notif-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.72rem;
        letter-spacing: 0.01em;
    }
    .notif-time-badge {
        color: #94A3B8;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Row Actions */
    .notif-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
        margin-left: auto;
    }
    .btn-action-pill {
        border-radius: 7px;
        padding: 5px 10px;
        font-size: 0.78rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: all 0.15s ease;
        text-decoration: none !important;
        border: 1px solid transparent;
        height: 32px;
    }
    .btn-action-primary {
        background-color: #EFF6FF;
        color: #1D4ED8;
        border-color: #BFDBFE;
    }
    .btn-action-primary:hover {
        background-color: #1D4ED8;
        color: #FFFFFF;
        border-color: #1D4ED8;
    }
    .btn-action-icon {
        width: 32px;
        height: 32px;
        border-radius: 7px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #F8FAFC;
        border: 1px solid #CBD5E1;
        color: #475569;
        transition: all 0.15s ease;
        cursor: pointer;
    }
    .btn-action-icon:hover {
        background: #FFFFFF;
        color: #0F172A;
        border-color: #94A3B8;
        transform: translateY(-1px);
    }
    .btn-action-icon.btn-action-delete:hover {
        background: #FEF2F2;
        color: #DC2626;
        border-color: #FCA5A5;
    }

    /* Unread Pulse Indicator */
    .unread-indicator-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #2563EB;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        display: inline-block;
        flex-shrink: 0;
    }

    /* Empty State */
    .notif-empty-state {
        padding: 4.5rem 1.5rem;
        text-align: center;
    }
    .empty-state-icon-box {
        width: 72px;
        height: 72px;
        border-radius: 20px;
        background: #EFF6FF;
        color: #2563EB;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1.25rem;
        border: 1px solid #BFDBFE;
    }

    /* Bulk Action Bar */
    .bulk-selection-bar {
        background: #0F1E4D;
        color: #FFFFFF;
        border-radius: 10px;
        padding: 10px 18px;
        margin-bottom: 1rem;
        display: none;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 6px 20px rgba(15, 30, 77, 0.25);
        animation: fadeInDown 0.2s ease;
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="content-body notif-container">
    <div class="container-fluid">
        
        <!-- Header & Breadcrumb Row -->
        <div class="row page-titles mb-4 align-items-center">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h4 class="mb-0 fw-bold" style="color: #0F172A;">
                            <i class="bi bi-bell-fill text-primary me-2"></i>Notifications Center
                        </h4>
                        <span class="header-badge-pill">
                            <span class="live-pulse-dot"></span> Live Operations Feed
                        </span>
                    </div>
                    <p class="mb-0" style="color: #475569; font-size: 0.86rem; font-weight: 500;">
                        System activity updates, lease scrutiny alerts, survey completions, and financial milestones.
                    </p>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex align-items-center gap-2">
                <ol class="breadcrumb mb-0 me-2 d-none d-lg-flex">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none" style="color: #475569; font-weight: 600;">Home</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold">Notifications</li>
                </ol>

                <!-- Mark All Read Action -->
                @if($stats['unread'] > 0)
                <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" id="btnMarkAllRead" style="height: 36px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-check2-all"></i> Mark All as Read
                </button>
                @endif

                <!-- More Options Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="height: 36px; border-radius: 8px; font-weight: 600;">
                        <i class="bi bi-three-dots-vertical"></i> Options
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="font-size: 0.84rem; border-radius: 10px;">
                        <li>
                            <form method="POST" action="{{ route('notifications.clear-all') }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="scope" value="read">
                                <button type="submit" class="dropdown-item py-2 text-secondary" onclick="return confirm('Clear all read notifications?');">
                                    <i class="bi bi-check-circle me-2 text-muted"></i> Clear Read Alerts
                                </button>
                            </form>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('notifications.clear-all') }}">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="scope" value="all">
                                <button type="submit" class="dropdown-item py-2 text-danger" onclick="return confirm('Clear ALL notifications? This cannot be undone.');">
                                    <i class="bi bi-trash3 me-2"></i> Clear All Alerts
                                </button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('notifications.seed-demo') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-primary">
                                    <i class="bi bi-arrow-clockwise me-2"></i> Reload Sample Alerts
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Session Flash Feedback Alerts -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2.5 px-3 mb-3 border-0 shadow-sm" role="alert" style="border-radius: 10px;">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Bento-Style KPI Metric Cards Grid -->
        <div class="row g-3 mb-4">
            <!-- Total Notifications -->
            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi-pro kpi-border-blue">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-eyebrow">Total Notifications</div>
                            <h3 class="kpi-value" id="kpiTotal">{{ number_format($stats['total']) }}</h3>
                            <div class="kpi-subtext">
                                <i class="bi bi-inbox text-primary"></i> Current inbox volume
                            </div>
                        </div>
                        <div class="kpi-icon-box" style="background: #EFF6FF; color: #1D4ED8;">
                            <i class="bi bi-bell"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Unread Alerts -->
            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi-pro kpi-border-amber">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-eyebrow">Unread Alerts</div>
                            <h3 class="kpi-value text-warning" id="kpiUnread">{{ number_format($stats['unread']) }}</h3>
                            <div class="kpi-subtext">
                                <i class="bi bi-exclamation-circle text-warning"></i> Requiring attention
                            </div>
                        </div>
                        <div class="kpi-icon-box" style="background: #FFFBEB; color: #D97706;">
                            <i class="bi bi-envelope-exclamation"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Required / High Priority -->
            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi-pro kpi-border-emerald">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-eyebrow">Lease & Mining</div>
                            <h3 class="kpi-value" style="color: #059669;">{{ number_format($stats['lease'] + $stats['mining']) }}</h3>
                            <div class="kpi-subtext">
                                <i class="bi bi-file-earmark-ruled text-success"></i> Statutory workflow items
                            </div>
                        </div>
                        <div class="kpi-icon-box" style="background: #ECFDF5; color: #059669;">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Surveys & Operations -->
            <div class="col-xl-3 col-sm-6">
                <div class="card card-kpi-pro kpi-border-purple">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-eyebrow">Technical Surveys</div>
                            <h3 class="kpi-value" style="color: #7C3AED;">{{ number_format($stats['survey']) }}</h3>
                            <div class="kpi-subtext">
                                <i class="bi bi-geo-alt text-purple"></i> DGPS & Drone mapping
                            </div>
                        </div>
                        <div class="kpi-icon-box" style="background: #FAF5FF; color: #7C3AED;">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="filter-toolbar-card">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                
                <!-- Status Pills -->
                <ul class="nav nav-pills nav-pills-custom mb-0" id="notifStatusTabs">
                    <li class="nav-item">
                        <a class="nav-link {{ $filter === 'all' ? 'active' : '' }}" href="{{ route('notifications.index', array_merge(request()->query(), ['filter' => 'all'])) }}">
                            All Alerts <span class="badge-count">{{ $stats['total'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $filter === 'unread' ? 'active' : '' }}" href="{{ route('notifications.index', array_merge(request()->query(), ['filter' => 'unread'])) }}">
                            Unread <span class="badge-count" id="tabUnreadCount">{{ $stats['unread'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $filter === 'read' ? 'active' : '' }}" href="{{ route('notifications.index', array_merge(request()->query(), ['filter' => 'read'])) }}">
                            Read <span class="badge-count">{{ $stats['read'] }}</span>
                        </a>
                    </li>
                </ul>

                <!-- Filter Controls: Category & Search -->
                <form method="GET" action="{{ route('notifications.index') }}" class="d-flex align-items-center gap-2 flex-wrap" id="filterForm">
                    <input type="hidden" name="filter" value="{{ $filter }}">

                    <!-- Category Filter Dropdown -->
                    <select name="category" class="form-select form-select-sm" style="height: 38px; min-width: 170px; border-radius: 8px; font-weight: 600; font-size: 0.83rem; border: 1.5px solid #CBD5E1; background-color: #F8FAFC;" onchange="document.getElementById('filterForm').submit();">
                        <option value="all" {{ $category === 'all' ? 'selected' : '' }}>All Departments</option>
                        <option value="lease" {{ $category === 'lease' ? 'selected' : '' }}>Lease Applications ({{ $stats['lease'] }})</option>
                        <option value="mining" {{ $category === 'mining' ? 'selected' : '' }}>Mining Department ({{ $stats['mining'] }})</option>
                        <option value="survey" {{ $category === 'survey' ? 'selected' : '' }}>Surveys & Demarcation ({{ $stats['survey'] }})</option>
                        <option value="accounts" {{ $category === 'accounts' ? 'selected' : '' }}>Accounts & Billing ({{ $stats['accounts'] }})</option>
                        <option value="environment" {{ $category === 'environment' ? 'selected' : '' }}>Environment EC ({{ $stats['environment'] }})</option>
                    </select>

                    <!-- Search Input -->
                    <div class="search-input-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search alerts, IDs, clients..." value="{{ $search }}" onkeydown="if(event.key === 'Enter'){ document.getElementById('filterForm').submit(); }">
                    </div>

                    @if(!empty($search) || $category !== 'all' || $filter !== 'all')
                    <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-danger" title="Clear all filters" style="height: 38px; border-radius: 8px; display: inline-flex; align-items: center;">
                        <i class="bi bi-x-circle me-1"></i> Reset
                    </a>
                    @endif
                </form>

            </div>
        </div>

        <!-- Bulk Selection Action Bar (Shown when checkboxes are selected) -->
        <div class="bulk-selection-bar" id="bulkActionBar">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check2-square fs-5"></i>
                <span class="fw-bold" id="bulkSelectedCount">0 items selected</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-light text-dark fw-bold" id="btnBulkMarkRead">
                    <i class="bi bi-envelope-open me-1"></i> Mark as Read
                </button>
                <button type="button" class="btn btn-sm btn-danger fw-bold" id="btnBulkDelete">
                    <i class="bi bi-trash3 me-1"></i> Delete
                </button>
                <button type="button" class="btn btn-sm btn-outline-light" id="btnBulkCancel">Cancel</button>
            </div>
        </div>

        <!-- Notification List Section -->
        <div class="notif-card-group" id="notifListContainer">
            @forelse($notifications as $notif)
                @php
                    $isUnread = is_null($notif->read_at);
                    $style = $notif->style_config;
                    $actionUrl = $notif->action_url ?? '#';
                    $entityId = $notif->data['entity_id'] ?? null;
                    $priority = $notif->data['priority'] ?? 'normal';
                @endphp
                <div class="notif-row {{ $isUnread ? 'is-unread' : '' }}" id="notif-row-{{ $notif->id }}" data-id="{{ $notif->id }}" data-unread="{{ $isUnread ? 'true' : 'false' }}">
                    
                    <!-- Selection Checkbox -->
                    <div class="form-check pt-1" style="margin-right: -4px;">
                        <input class="form-check-input notif-select-checkbox" type="checkbox" value="{{ $notif->id }}" style="cursor: pointer; width: 17px; height: 17px;">
                    </div>

                    <!-- Category Icon Badge -->
                    <div class="notif-avatar" style="background-color: {{ $style['bg'] }}; color: {{ $style['text'] }}; border: 1px solid {{ $style['border'] }};">
                        <i class="bi {{ $notif->icon }}"></i>
                    </div>

                    <!-- Content & Metadata -->
                    <div class="notif-content-area">
                        <div class="notif-header-line">
                            <h5 class="notif-item-title">
                                @if($isUnread)
                                    <span class="unread-indicator-dot" title="Unread notification"></span>
                                @endif
                                <span>{{ $notif->title }}</span>
                            </h5>
                            <span class="notif-time-badge" title="{{ $notif->created_at ? $notif->created_at->format('d M Y, h:i A') : '' }}">
                                <i class="bi bi-clock"></i> {{ $notif->time_ago }}
                            </span>
                        </div>

                        <p class="notif-body-text">
                            {{ $notif->message }}
                        </p>

                        <!-- Chips Strip -->
                        <div class="notif-meta-strip">
                            <!-- Category Badge -->
                            <span class="notif-tag" style="background-color: {{ $style['bg'] }}; color: {{ $style['text'] }}; border: 1px solid {{ $style['border'] }};">
                                <i class="bi {{ $notif->icon }}"></i> {{ $style['label'] }}
                            </span>

                            @if($entityId)
                            <span class="notif-tag bg-light text-dark border">
                                <i class="bi bi-hash"></i> {{ $entityId }}
                            </span>
                            @endif

                            @if($priority === 'urgent')
                            <span class="notif-tag bg-danger-subtle text-danger border border-danger-subtle">
                                <i class="bi bi-fire"></i> Urgent Priority
                            </span>
                            @elseif($priority === 'high')
                            <span class="notif-tag bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                <i class="bi bi-exclamation-triangle"></i> Action Required
                            </span>
                            @endif

                            <span class="text-muted" style="font-size: 0.72rem;">
                                {{ $notif->created_at ? $notif->created_at->format('M d, Y • h:i A') : '' }}
                            </span>
                        </div>
                    </div>

                    <!-- Action Controls -->
                    <div class="notif-actions">
                        @if(!empty($notif->action_url))
                        <a href="{{ $notif->action_url }}" class="btn-action-pill btn-action-primary" title="Open and process target item">
                            <span>Open</span> <i class="bi bi-arrow-right"></i>
                        </a>
                        @endif

                        <!-- Toggle Read / Unread Status -->
                        <button type="button" class="btn-action-icon btn-toggle-read" data-id="{{ $notif->id }}" data-status="{{ $isUnread ? 'unread' : 'read' }}" title="{{ $isUnread ? 'Mark as Read' : 'Mark as Unread' }}">
                            <i class="bi {{ $isUnread ? 'bi-check-lg' : 'bi-envelope' }}"></i>
                        </button>

                        <!-- Delete Notification -->
                        <button type="button" class="btn-action-icon btn-action-delete btn-delete-notif" data-id="{{ $notif->id }}" title="Delete notification">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>

                </div>
            @empty
                <!-- Empty State -->
                <div class="notif-empty-state">
                    <div class="empty-state-icon-box">
                        <i class="bi bi-bell-slash"></i>
                    </div>
                    <h5 class="fw-bold mb-1" style="color: #0F172A;">All caught up! No notifications found</h5>
                    <p class="text-muted mb-3 mx-auto" style="max-width: 440px; font-size: 0.88rem;">
                        @if(!empty($search) || $category !== 'all' || $filter !== 'all')
                            No alerts match your current filter criteria. Try resetting filters to see other alerts.
                        @else
                            You have no pending notifications at this moment. You will be alerted when new lease applications, survey reviews, or account vouchers are submitted.
                        @endif
                    </p>
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        @if(!empty($search) || $category !== 'all' || $filter !== 'all')
                            <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-primary" style="font-weight: 700; border-radius: 8px;">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Filters
                            </a>
                        @endif
                        <form method="POST" action="{{ route('notifications.seed-demo') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary" style="font-weight: 700; border-radius: 8px;">
                                <i class="bi bi-magic me-1"></i> Load Demo Alerts
                            </button>
                        </form>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination Controls -->
        @if($notifications->hasPages())
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-4 px-2">
            <div class="text-muted" style="font-size: 0.82rem; font-weight: 500;">
                Showing <span class="fw-bold text-dark">{{ $notifications->firstItem() }}</span> to <span class="fw-bold text-dark">{{ $notifications->lastItem() }}</span> of <span class="fw-bold text-dark">{{ $notifications->total() }}</span> alerts
            </div>
            <div>
                {{ $notifications->links() }}
            </div>
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Helper: Header Notification Badge Updater
    function updateHeaderBadges(unreadCount) {
        // Update header indicator
        const bellIndicator = document.querySelector('.header-action-btn .notification-indicator');
        if (unreadCount > 0) {
            if (!bellIndicator) {
                const bellBtn = document.querySelector('.notification_dropdown .header-action-btn');
                if (bellBtn) {
                    const dot = document.createElement('span');
                    dot.className = 'notification-indicator';
                    bellBtn.appendChild(dot);
                }
            }
        } else {
            if (bellIndicator) bellIndicator.remove();
        }

        // Update dropdown header badge
        const headerBadge = document.querySelector('.notification_dropdown .dropdown-menu .badge');
        if (headerBadge) {
            if (unreadCount > 0) {
                headerBadge.textContent = unreadCount + ' New';
                headerBadge.style.background = '#eff6ff';
                headerBadge.style.color = '#2563eb';
            } else {
                headerBadge.textContent = 'All Caught Up';
                headerBadge.style.background = '#f8fafc';
                headerBadge.style.color = '#64748b';
            }
        }

        // Update sidebar badge
        const sideBadge = document.querySelector('.dlabnav .fa-bell + .nav-text + .badge, .dlabnav a[href*="notifications"] .badge');
        if (sideBadge) {
            if (unreadCount > 0) {
                sideBadge.textContent = unreadCount;
                sideBadge.style.display = 'inline-block';
            } else {
                sideBadge.style.display = 'none';
            }
        }

        // Update page KPI
        const kpiUnread = document.getElementById('kpiUnread');
        if (kpiUnread) kpiUnread.textContent = unreadCount;

        const tabUnread = document.getElementById('tabUnreadCount');
        if (tabUnread) tabUnread.textContent = unreadCount;
    }

    // Toggle Read / Unread Status via AJAX
    document.querySelectorAll('.btn-toggle-read').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const currentStatus = this.getAttribute('data-status');
            const row = document.getElementById('notif-row-' + id);
            const isUnread = (currentStatus === 'unread');
            const endpoint = isUnread 
                ? `/notifications/${id}/mark-as-read` 
                : `/notifications/${id}/mark-as-unread`;

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (isUnread) {
                        row.classList.remove('is-unread');
                        row.setAttribute('data-unread', 'false');
                        this.setAttribute('data-status', 'read');
                        this.setAttribute('title', 'Mark as Unread');
                        this.innerHTML = '<i class="bi bi-envelope"></i>';
                        const dot = row.querySelector('.unread-indicator-dot');
                        if (dot) dot.remove();
                        if (typeof toastr !== 'undefined') toastr.success('Alert marked as read');
                    } else {
                        row.classList.add('is-unread');
                        row.setAttribute('data-unread', 'true');
                        this.setAttribute('data-status', 'unread');
                        this.setAttribute('title', 'Mark as Read');
                        this.innerHTML = '<i class="bi bi-check-lg"></i>';
                        const titleEl = row.querySelector('.notif-item-title');
                        if (titleEl && !titleEl.querySelector('.unread-indicator-dot')) {
                            const newDot = document.createElement('span');
                            newDot.className = 'unread-indicator-dot';
                            newDot.title = 'Unread notification';
                            titleEl.prepend(newDot);
                        }
                        if (typeof toastr !== 'undefined') toastr.info('Alert marked as unread');
                    }
                    if (typeof data.unread_count !== 'undefined') {
                        updateHeaderBadges(data.unread_count);
                    }
                }
            })
            .catch(err => {
                console.error(err);
                if (typeof toastr !== 'undefined') toastr.error('Failed to update alert');
            });
        });
    });

    // Delete Single Notification via AJAX
    document.querySelectorAll('.btn-delete-notif').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to delete this notification?')) return;

            const id = this.getAttribute('data-id');
            const row = document.getElementById('notif-row-' + id);

            fetch(`/notifications/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '0';
                    row.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        row.remove();
                        // Update total count
                        const kpiTotal = document.getElementById('kpiTotal');
                        if (kpiTotal) {
                            const currentVal = parseInt(kpiTotal.textContent.replace(/,/g, '')) || 0;
                            kpiTotal.textContent = Math.max(0, currentVal - 1);
                        }
                    }, 300);

                    if (typeof toastr !== 'undefined') toastr.success('Notification removed');
                    if (typeof data.unread_count !== 'undefined') {
                        updateHeaderBadges(data.unread_count);
                    }
                }
            })
            .catch(err => {
                console.error(err);
                if (typeof toastr !== 'undefined') toastr.error('Failed to delete alert');
            });
        });
    });

    // Mark All As Read
    const btnMarkAll = document.getElementById('btnMarkAllRead');
    if (btnMarkAll) {
        btnMarkAll.addEventListener('click', function () {
            fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll('.notif-row.is-unread').forEach(row => {
                        row.classList.remove('is-unread');
                        row.setAttribute('data-unread', 'false');
                        const dot = row.querySelector('.unread-indicator-dot');
                        if (dot) dot.remove();
                        const toggleBtn = row.querySelector('.btn-toggle-read');
                        if (toggleBtn) {
                            toggleBtn.setAttribute('data-status', 'read');
                            toggleBtn.setAttribute('title', 'Mark as Unread');
                            toggleBtn.innerHTML = '<i class="bi bi-envelope"></i>';
                        }
                    });

                    btnMarkAll.style.display = 'none';
                    updateHeaderBadges(0);
                    if (typeof toastr !== 'undefined') toastr.success('All notifications marked as read');
                }
            })
            .catch(err => console.error(err));
        });
    }

    // Checkbox & Bulk Actions Logic
    const checkboxes = document.querySelectorAll('.notif-select-checkbox');
    const bulkBar = document.getElementById('bulkActionBar');
    const bulkCountText = document.getElementById('bulkSelectedCount');

    function refreshBulkBar() {
        const selected = document.querySelectorAll('.notif-select-checkbox:checked');
        if (selected.length > 0) {
            bulkBar.style.display = 'flex';
            bulkCountText.textContent = `${selected.length} alert${selected.length > 1 ? 's' : ''} selected`;
        } else {
            bulkBar.style.display = 'none';
        }
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', refreshBulkBar);
    });

    const btnBulkCancel = document.getElementById('btnBulkCancel');
    if (btnBulkCancel) {
        btnBulkCancel.addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = false);
            refreshBulkBar();
        });
    }

    // Bulk Mark as Read
    const btnBulkMarkRead = document.getElementById('btnBulkMarkRead');
    if (btnBulkMarkRead) {
        btnBulkMarkRead.addEventListener('click', function () {
            const selected = Array.from(document.querySelectorAll('.notif-select-checkbox:checked')).map(cb => cb.value);
            if (!selected.length) return;

            Promise.all(selected.map(id => {
                return fetch(`/notifications/${id}/mark-as-read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
            })).then(() => {
                selected.forEach(id => {
                    const row = document.getElementById('notif-row-' + id);
                    if (row) {
                        row.classList.remove('is-unread');
                        const dot = row.querySelector('.unread-indicator-dot');
                        if (dot) dot.remove();
                    }
                });
                checkboxes.forEach(cb => cb.checked = false);
                refreshBulkBar();
                if (typeof toastr !== 'undefined') toastr.success('Selected alerts marked as read');
                window.location.reload();
            });
        });
    }

    // Bulk Delete
    const btnBulkDelete = document.getElementById('btnBulkDelete');
    if (btnBulkDelete) {
        btnBulkDelete.addEventListener('click', function () {
            const selected = Array.from(document.querySelectorAll('.notif-select-checkbox:checked')).map(cb => cb.value);
            if (!selected.length) return;
            if (!confirm(`Are you sure you want to delete ${selected.length} selected notifications?`)) return;

            Promise.all(selected.map(id => {
                return fetch(`/notifications/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
            })).then(() => {
                selected.forEach(id => {
                    const row = document.getElementById('notif-row-' + id);
                    if (row) row.remove();
                });
                checkboxes.forEach(cb => cb.checked = false);
                refreshBulkBar();
                if (typeof toastr !== 'undefined') toastr.success('Selected alerts removed');
                window.location.reload();
            });
        });
    }

});
</script>
@endpush
@endsection

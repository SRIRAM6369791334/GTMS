<!-- GTMS Enterprise SaaS Header Component (ui-ux-pro-max standard) -->
<style>
    /* =========================================================
       GTMS HEADER STYLING (Harmonized with Dexignlabs Theme)
       ========================================================= */
    .nav-header {
        background-color: #ffffff !important;
        border-bottom: 1px solid #eef2f6 !important;
        border-right: 1px solid #eef2f6 !important;
    }

    .nav-header .brand-logo {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        padding-left: 1.5rem !important;
    }

    .nav-header .brand-logo img {
        height: 38px !important;
        width: auto !important;
        object-fit: contain !important;
    }

    .nav-header .brand-text {
        font-size: 1.2rem;
        font-weight: 800;
        color: #1b2653;
        letter-spacing: -0.02em;
        margin-left: 0.6rem;
    }

    .header {
        background-color: #ffffff !important;
        border-bottom: 1px solid #eef2f6 !important;
        box-shadow: 0 1px 3px 0 rgba(15, 23, 42, 0.03) !important;
    }

    .header-left .dashboard_bar {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        letter-spacing: -0.02em !important;
    }

    /* SEARCH INPUT */
    .header-search-wrap {
        position: relative;
        display: flex;
        align-items: center;
        width: 250px;
        transition: width 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .header-search-wrap:focus-within {
        width: 320px;
    }

    .header-search-wrap .search-icon {
        position: absolute;
        left: 12px;
        color: #94a3b8;
        font-size: 0.88rem;
        pointer-events: none;
        transition: color 0.2s ease;
    }

    .header-search-wrap:focus-within .search-icon {
        color: #1b2653;
    }

    .header-search-input {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 0.45rem 1rem 0.45rem 2.25rem !important;
        font-size: 0.82rem !important;
        color: #0f172a !important;
        height: 38px !important;
        transition: all 0.2s ease !important;
    }

    .header-search-input:focus {
        background: #ffffff !important;
        border-color: #1b2653 !important;
        box-shadow: 0 0 0 3px rgba(27, 38, 83, 0.08) !important;
    }

    .header-search-input::placeholder {
        color: #94a3b8 !important;
        font-size: 0.82rem !important;
    }

    /* ACTION BUTTONS (Theme Toggle & Notification Bell) */
    .header-action-btn {
        width: 38px !important;
        height: 38px !important;
        border-radius: 10px !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #475569 !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
        position: relative !important;
        padding: 0 !important;
    }

    .header-action-btn:hover {
        background: #eff6ff !important;
        border-color: #cbd5e1 !important;
        color: #1b2653 !important;
    }

    .header-action-btn i {
        font-size: 1.05rem !important;
    }

    .notification-indicator {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 8px;
        height: 8px;
        background: #ef4444;
        border-radius: 50%;
        border: 1.5px solid #ffffff;
    }

    /* DROPDOWNS STRICT ANCHORING & ELEVATION */
    .header-right .notification_dropdown,
    .header-right .header-profile {
        position: relative !important;
    }

    .header-right .notification_dropdown .dropdown-menu,
    .header-right .header-profile .dropdown-menu {
        position: absolute !important;
        right: 0 !important;
        left: auto !important;
        top: calc(100% + 8px) !important;
        margin: 0 !important;
        transform: none !important;
    }

    .header-menu-card {
        border-radius: 14px !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        box-shadow: 
            0 4px 6px -1px rgba(15, 23, 42, 0.05),
            0 20px 25px -5px rgba(15, 23, 42, 0.08) !important;
        overflow: hidden;
    }

    /* NOTIFICATION CARD STYLING */
    .notif-item {
        transition: background 0.15s ease;
        text-decoration: none !important;
        padding: 0.75rem 1rem !important;
    }

    .notif-item:hover {
        background: #f8fafc !important;
    }

    .notif-icon-badge {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1rem;
    }

    .notif-title {
        font-size: 0.82rem;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.25;
    }

    .notif-desc {
        font-size: 0.76rem;
        color: #475569;
        line-height: 1.35;
        margin-top: 2px;
    }

    .notif-time {
        font-size: 0.7rem;
        color: #94a3b8;
        margin-top: 3px;
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .notif-footer-link {
        color: #1b2653;
        font-weight: 600;
        font-size: 0.78rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: color 0.15s ease;
    }

    .notif-footer-link:hover {
        color: #2a3875;
        text-decoration: underline;
    }

    /* USER PROFILE PILL */
    .user-profile-pill {
        padding: 0.28rem 0.7rem 0.28rem 0.32rem !important;
        border-radius: 9999px !important;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.55rem !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
        cursor: pointer;
    }

    .user-profile-pill:hover {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05) !important;
    }

    .user-avatar-initials {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1b2653 0%, #2e4182 100%);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 1px 3px rgba(27, 38, 83, 0.2);
    }

    .user-avatar-img {
        width: 32px;
        height: 32px;
        object-fit: cover;
        border-radius: 50%;
    }

    .user-name-text {
        font-size: 0.82rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .user-role-chip {
        font-size: 0.68rem;
        color: #1b2653;
        background: #eff4fe;
        border: 1px solid #dbe6fe;
        border-radius: 4px;
        padding: 1px 5px;
        font-weight: 600;
        display: inline-block;
    }
</style>

<!-- Top Left: Logo Brand & Sidebar Control -->
<div class="nav-header">
    <a href="/" class="brand-logo">
        <img src="{{ asset('images/gtmslogo.png') }}" alt="GTMS Logo" />
        <span class="brand-text d-none d-sm-inline-block">GTMS</span>
    </a>
    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>

<!-- Main Top Header -->
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                
                <!-- Left Title -->
                <div class="header-left">
                    <div class="dashboard_bar">
                        @yield('title', 'Dashboard')
                    </div>
                </div>

                <!-- Right Actions -->
                <ul class="navbar-nav header-right align-items-center gap-2">
                    
                    <!-- Search Input -->
                    <li class="nav-item d-none d-lg-flex align-items-center me-2">
                        <div class="header-search-wrap">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" class="form-control header-search-input" placeholder="Search applications, clients...">
                        </div>
                    </li>

                    <!-- Dark / Light Theme Mode Toggle -->
                    <li class="nav-item">
                        <a class="header-action-btn dz-theme-mode" href="javascript:void(0);" title="Toggle theme">
                            <i id="icon-light" class="fas fa-sun text-warning"></i>
                            <i id="icon-dark" class="fas fa-moon text-secondary"></i>
                        </a>
                    </li>

                    <!-- Notifications Dropdown -->
                    <li class="nav-item dropdown notification_dropdown">
                        <a class="header-action-btn" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" title="Notifications">
                            <i class="bi bi-bell"></i>
                            <span class="notification-indicator"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end header-menu-card p-0" style="min-width: 320px; width: 320px;">
                            <div class="d-flex align-items-center justify-content-between px-3 py-2.5 border-bottom bg-white">
                                <h6 class="mb-0 fw-bold" style="font-size: 0.88rem; color: #0f172a;">Notifications</h6>
                                <span class="badge" style="background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; font-size: 0.72rem; font-weight: 600; border-radius: 9999px; padding: 2px 8px;">3 New</span>
                            </div>
                            <div class="py-1 bg-white" style="max-height: 290px; overflow-y: auto;">
                                <a href="{{ route('application.index') }}" class="dropdown-item notif-item d-flex align-items-start gap-2.5 border-bottom border-light">
                                    <div class="notif-icon-badge" style="background: #eff6ff; color: #2563eb;">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="notif-title">New Lease Application</div>
                                        <div class="notif-desc">Application submitted for scrutiny review</div>
                                        <div class="notif-time"><i class="bi bi-clock"></i> 10m ago</div>
                                    </div>
                                </a>
                                <a href="{{ route('miningplan.index') }}" class="dropdown-item notif-item d-flex align-items-start gap-2.5 border-bottom border-light">
                                    <div class="notif-icon-badge" style="background: #fffbeb; color: #d97706;">
                                        <i class="bi bi-geo-alt"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="notif-title">Mining Plan Update</div>
                                        <div class="notif-desc">Draft document uploaded for verification</div>
                                        <div class="notif-time"><i class="bi bi-clock"></i> 1h ago</div>
                                    </div>
                                </a>
                                <a href="{{ route('dgps-survey.index') }}" class="dropdown-item notif-item d-flex align-items-start gap-2.5">
                                    <div class="notif-icon-badge" style="background: #ecfdf5; color: #059669;">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="notif-title">Survey Completed</div>
                                        <div class="notif-desc">DGPS survey boundary coordinates approved</div>
                                        <div class="notif-time"><i class="bi bi-clock"></i> Yesterday</div>
                                    </div>
                                </a>
                            </div>
                            <div class="px-3 py-2 border-top text-center" style="background: #f8fafc;">
                                <a href="{{ route('application.index') }}" class="notif-footer-link">
                                    <span>View All Applications</span>
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </li>

                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown header-profile ms-1">
                        <a class="user-profile-pill" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                            @if(Auth::check() && Auth::user()->image && file_exists(public_path('uploads/users/' . Auth::user()->image)))
                                <img src="{{ asset('uploads/users/' . Auth::user()->image) }}" class="user-avatar-img" alt="User Image">
                            @else
                                <div class="user-avatar-initials">
                                    {{ strtoupper(substr(Auth::check() ? Auth::user()->name : 'A', 0, 1)) }}
                                </div>
                            @endif
                            <div class="d-none d-sm-block text-start" style="line-height: 1.15;">
                                <span class="user-name-text d-block">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</span>
                                <span class="user-role-chip">{{ Auth::check() && Auth::user()->roles->first() ? Auth::user()->roles->first()->name : (Auth::check() && Auth::user()->role ? Auth::user()->role->name : 'Admin') }}</span>
                            </div>
                            <i class="bi bi-chevron-down d-none d-sm-inline-block text-muted" style="font-size: 0.72rem; margin-right: 2px;"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-end header-menu-card p-0" style="min-width: 220px;">
                            <div class="p-3 border-bottom bg-white">
                                <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ Auth::check() ? Auth::user()->name : 'Guest' }}</div>
                                <div class="text-muted text-truncate" style="font-size: 0.76rem;">{{ Auth::check() ? Auth::user()->email : 'admin@gtms.com' }}</div>
                            </div>
                            <div class="py-1 bg-white">
                                <a href="{{ route('user.index') }}" class="dropdown-item d-flex align-items-center gap-2 px-3 py-2 text-dark" style="font-size: 0.82rem;">
                                    <i class="bi bi-people text-muted"></i>
                                    <span>User Management</span>
                                </a>
                                <a href="{{ route('roles.index') }}" class="dropdown-item d-flex align-items-center gap-2 px-3 py-2 text-dark" style="font-size: 0.82rem;">
                                    <i class="bi bi-shield-check text-muted"></i>
                                    <span>Roles & Permissions</span>
                                </a>
                            </div>
                            <div class="border-top py-1 bg-white">
                                <form method="POST" action="{{ route('logout') }}" id="header-logout-form" style="display: none;">
                                    @csrf
                                </form>
                                <a href="javascript:void(0);" onclick="document.getElementById('header-logout-form').submit();" class="dropdown-item d-flex align-items-center gap-2 px-3 py-2 text-danger" style="font-size: 0.82rem;">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Sign Out</span>
                                </a>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>
        </nav>
    </div>
</div>

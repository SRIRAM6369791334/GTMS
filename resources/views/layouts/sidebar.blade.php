<!-- GTMS Enterprise SaaS Sidebar Component (ui-ux-pro-max standard) -->
<style>
    /* =========================================================
       GTMS ENTERPRISE SAAS SIDEBAR (UI-UX-PRO-MAX STANDARD)
       ========================================================= */
    :root {
        --sb-bg: #ffffff;
        --sb-border: #eef2f6;
        --sb-text: #475569;
        --sb-text-hover: #0F1E4D;
        --sb-hover-bg: #f8fafc;
        --sb-icon-bg: #f1f5f9;
        --sb-icon-border: #e2e8f0;
        --sb-icon-color: #64748b;
        --sb-icon-hover-color: #0F1E4D;
        --sb-active-bg: #eff6ff;
        --sb-active-text: #0F1E4D;
        --sb-active-icon-bg: #dbeafe;
        --sb-active-shadow: none;
        --sb-active-border: #2563eb;
        --sb-sub-line: #e2e8f0;
        --sb-sub-text: #64748b;
        --sb-sub-hover-bg: #f8fafc;
        --sb-sub-hover-text: #0F1E4D;
        --sb-sub-active-bg: #eff6ff;
        --sb-sub-active-text: #0F1E4D;
    }

    body[data-theme-version="dark"] {
        --sb-bg: #161717;
        --sb-border: #2d3139;
        --sb-text: #94a3b8;
        --sb-text-hover: #f8fafc;
        --sb-hover-bg: rgba(255, 255, 255, 0.05);
        --sb-icon-bg: rgba(255, 255, 255, 0.04);
        --sb-icon-border: rgba(255, 255, 255, 0.08);
        --sb-icon-color: #94a3b8;
        --sb-icon-hover-color: #38bdf8;
        --sb-active-bg: rgba(37, 99, 235, 0.12);
        --sb-active-text: #93c5fd;
        --sb-active-icon-bg: rgba(59, 130, 246, 0.2);
        --sb-active-shadow: none;
        --sb-active-border: #3b82f6;
        --sb-sub-line: #2d3139;
        --sb-sub-text: #94a3b8;
        --sb-sub-hover-bg: rgba(255, 255, 255, 0.05);
        --sb-sub-hover-text: #f8fafc;
        --sb-sub-active-bg: rgba(59, 130, 246, 0.15);
        --sb-sub-active-text: #60a5fa;
    }

    /* Core Sidebar Container */
    .dlabnav {
        background-color: var(--sb-bg) !important;
        border-right: 1px solid var(--sb-border) !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        transition: width 0.25s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease !important;
    }

    /* Sleek Webkit Scrollbar */
    .dlabnav-scroll::-webkit-scrollbar {
        width: 4px !important;
    }
    .dlabnav-scroll::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.25) !important;
        border-radius: 4px !important;
    }
    .dlabnav-scroll::-webkit-scrollbar-track {
        background: transparent !important;
    }

    /* Metismenu Root List */
    .dlabnav .metismenu {
        padding: 0.85rem 0.5rem 4rem 0.5rem !important;
    }
    .dlabnav .metismenu > li {
        margin-bottom: 4px !important;
        position: relative !important;
    }

    /* Top Level Menu Links */
    .dlabnav .metismenu > li > a {
        display: flex !important;
        align-items: center !important;
        padding: 0.58rem 0.65rem !important;
        border-radius: 10px !important;
        font-size: 0.835rem !important;
        font-weight: 500 !important;
        color: var(--sb-text) !important;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        position: relative !important;
    }

    .dlabnav .metismenu > li > a:hover {
        background-color: var(--sb-hover-bg) !important;
        color: var(--sb-text-hover) !important;
        transform: translateX(2px);
    }

    /* Squircle Icon Wrapper */
    .dlabnav .metismenu > li > a i {
        width: 30px !important;
        height: 30px !important;
        border-radius: 8px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 0.92rem !important;
        margin-right: 0.55rem !important;
        flex-shrink: 0 !important;
        color: var(--sb-icon-color) !important;
        background: var(--sb-icon-bg) !important;
        border: 1px solid var(--sb-icon-border) !important;
        transition: all 0.2s ease !important;
    }

    .dlabnav .metismenu > li > a:hover i {
        color: var(--sb-icon-hover-color) !important;
        transform: scale(1.06);
    }

    /* Single-line No-wrap Text Fix for Environment Clearance */
    .dlabnav .metismenu > li > a .nav-text {
        font-size: 0.835rem !important;
        font-weight: inherit !important;
        white-space: nowrap !important;
        line-height: 1.25 !important;
        flex: 1 1 auto !important;
    }

    /* Active State (Top-Level) */
    .dlabnav .metismenu > li.mm-active > a {
        background: var(--sb-active-bg) !important;
        color: var(--sb-active-text) !important;
        box-shadow: var(--sb-active-shadow) !important;
        font-weight: 600 !important;
        border-left: 3px solid var(--sb-active-border) !important;
        border-radius: 0 10px 10px 0 !important;
    }
    .dlabnav .metismenu > li.mm-active > a i {
        background: var(--sb-active-icon-bg) !important;
        color: var(--sb-active-text) !important;
        border-color: transparent !important;
    }
    .dlabnav .metismenu > li.mm-active > a .nav-text {
        color: var(--sb-active-text) !important;
    }

    /* Chevron Indicator (.has-arrow:after) */
    .dlabnav .metismenu li > a.has-arrow:after {
        width: 0.42rem !important;
        height: 0.42rem !important;
        border-width: 1.8px 1.8px 0 0 !important;
        border-style: solid !important;
        border-color: var(--sb-icon-color) !important;
        right: 0.75rem !important;
        top: 50% !important;
        margin-top: -0.21rem !important;
        transform: rotate(45deg) !important;
        transition: transform 0.2s ease, border-color 0.2s ease !important;
    }
    .dlabnav .metismenu li.mm-active > a.has-arrow:after {
        border-color: var(--sb-active-text) !important;
        transform: rotate(135deg) !important;
    }
    .dlabnav .metismenu li > a.has-arrow[aria-expanded="true"]:after {
        transform: rotate(135deg) !important;
        border-color: var(--sb-text-hover) !important;
    }

    /* Submenus (Tree view dropdowns) */
    .dlabnav .metismenu ul {
        background: transparent !important;
        border-left: 2px solid var(--sb-sub-line) !important;
        margin: 0.25rem 0 0.45rem 2rem !important;
        padding: 0.2rem 0 0.2rem 0.5rem !important;
        list-style: none !important;
    }
    .dlabnav .metismenu ul li {
        margin: 2px 0 !important;
    }
    .dlabnav .metismenu ul li a {
        padding: 0.42rem 0.75rem !important;
        font-size: 0.815rem !important;
        font-weight: 500 !important;
        color: var(--sb-sub-text) !important;
        border-radius: 7px !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        text-decoration: none !important;
        transition: all 0.15s ease !important;
    }
    .dlabnav .metismenu ul li a::before {
        content: none !important;
        display: none !important;
    }
    /* Vendor theme override: kill dots from style.css [data-sidebar-style] rules */
    [data-sidebar-style="full"][data-layout="vertical"] .dlabnav .metismenu ul a:before,
    [data-sidebar-style="mini"] .dlabnav .metismenu ul a:before {
        content: none !important;
        display: none !important;
    }
    .dlabnav .metismenu ul li a:hover {
        background: var(--sb-sub-hover-bg) !important;
        color: var(--sb-sub-hover-text) !important;
        transform: translateX(3px) !important;
    }
    .dlabnav .metismenu ul li a:hover::before {
        content: none !important;
        display: none !important;
    }
    .dlabnav .metismenu ul li.mm-active > a {
        background: var(--sb-sub-active-bg) !important;
        color: var(--sb-sub-active-text) !important;
        font-weight: 600 !important;
    }
    .dlabnav .metismenu ul li.mm-active > a::before {
        content: none !important;
        display: none !important;
    }

    /* COLLAPSED / MINI SIDEBAR (when hamburger is touched) */
    #main-wrapper.menu-toggle .dlabnav {
        width: 5rem !important;
        overflow: visible !important;
    }
    #main-wrapper.menu-toggle .dlabnav .dlabnav-scroll {
        overflow: visible !important;
    }
    #main-wrapper.menu-toggle .dlabnav .metismenu {
        padding: 0.85rem 0.5rem 3rem 0.5rem !important;
        overflow: visible !important;
    }
    #main-wrapper.menu-toggle .dlabnav .metismenu > li {
        margin: 6px 0 !important;
        display: flex !important;
        justify-content: center !important;
        position: relative !important;
    }
    #main-wrapper.menu-toggle .dlabnav .metismenu > li > a {
        width: 44px !important;
        height: 44px !important;
        padding: 0 !important;
        margin: 0 auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 12px !important;
        background: transparent !important;
        box-shadow: none !important;
        transform: none !important;
        cursor: pointer !important;
        position: relative !important;
        z-index: 1052 !important;
    }
    #main-wrapper.menu-toggle .dlabnav .metismenu > li > a i {
        margin-right: 0 !important;
        width: 40px !important;
        height: 40px !important;
        font-size: 1.05rem !important;
        border-radius: 10px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        background: var(--sb-icon-bg) !important;
        border: 1px solid var(--sb-icon-border) !important;
        color: var(--sb-icon-color) !important;
        transition: all 0.2s ease !important;
    }
    #main-wrapper.menu-toggle .dlabnav .metismenu > li > a:hover i,
    #main-wrapper.menu-toggle .dlabnav .metismenu > li.flyout-open > a i {
        background: var(--sb-hover-bg) !important;
        color: var(--sb-text-hover) !important;
        transform: scale(1.08) !important;
    }
    #main-wrapper.menu-toggle .dlabnav .metismenu > li.mm-active > a {
        background: transparent !important;
        box-shadow: none !important;
    }
    #main-wrapper.menu-toggle .dlabnav .metismenu > li.mm-active > a i {
        background: var(--sb-active-bg) !important;
        color: var(--sb-active-text) !important;
        border-color: var(--sb-active-border) !important;
        border-width: 1.5px !important;
        box-shadow: none !important;
    }
    #main-wrapper.menu-toggle .dlabnav .metismenu > li > a.has-arrow:after {
        display: none !important;
    }

    /* Floating Popover Submenu in Collapsed Mode */
    #main-wrapper.menu-toggle .dlabnav .metismenu > li > ul {
        position: absolute !important;
        left: 4.8rem !important;
        top: 0 !important;
        width: 220px !important;
        min-width: 200px !important;
        background: var(--sb-bg) !important;
        border: 1px solid var(--sb-border) !important;
        border-radius: 12px !important;
        box-shadow: 0 12px 32px -4px rgba(15, 23, 42, 0.18), 0 4px 12px rgba(0,0,0,0.06) !important;
        padding: 0.5rem !important;
        z-index: 1050 !important;
        list-style: none !important;
        margin: 0 !important;
        display: none !important;
        pointer-events: auto !important;
    }
    /* Invisible hit-test bridge spanning the gap between sidebar icon and floating submenu */
    #main-wrapper.menu-toggle .dlabnav .metismenu > li > ul::before {
        content: '' !important;
        position: absolute !important;
        top: -10px !important;
        bottom: -10px !important;
        left: -20px !important;
        width: 25px !important;
        background: transparent !important;
        pointer-events: auto !important;
    }
    #main-wrapper.menu-toggle .dlabnav .metismenu > li:hover > ul,
    #main-wrapper.menu-toggle .dlabnav .metismenu > li.flyout-open > ul {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    #main-wrapper.menu-toggle .dlabnav .metismenu > li > ul li {
        margin: 2px 0 !important;
        width: 100% !important;
    }
    #main-wrapper.menu-toggle .dlabnav .metismenu > li > ul li a {
        padding: 0.52rem 0.85rem !important;
        font-size: 0.825rem !important;
        font-weight: 500 !important;
        color: var(--sb-text) !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        text-decoration: none !important;
        transition: all 0.15s ease !important;
        background: transparent !important;
        width: 100% !important;
        box-sizing: border-box !important;
        position: relative !important;
        z-index: 1053 !important;
        cursor: pointer !important;
    }
    #main-wrapper.menu-toggle .dlabnav .metismenu > li > ul li a:hover {
        background: var(--sb-sub-hover-bg) !important;
        color: var(--sb-sub-hover-text) !important;
        transform: translateX(3px) !important;
    }
    #main-wrapper.menu-toggle .dlabnav .metismenu > li > ul li.mm-active > a {
        background: var(--sb-sub-active-bg) !important;
        color: var(--sb-sub-active-text) !important;
        font-weight: 600 !important;
    }
    body[data-theme-version="dark"] #main-wrapper.menu-toggle .dlabnav .metismenu > li > ul {
        background: #1e1e24 !important;
        border-color: #2d3139 !important;
        box-shadow: 0 12px 32px -4px rgba(0, 0, 0, 0.6) !important;
    }
</style>

<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">

            @can('dashboard.view')
            <li class="{{ request()->is('dashboard*') ? 'mm-active' : '' }}"><a href="/dashboard" aria-expanded="false" class="{{ request()->is('dashboard*') ? 'mm-active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span class="nav-text">Home</span>
                </a>
            </li>
            <!-- @endcan
            <li class="{{ request()->routeIs('notifications.*') ? 'mm-active' : '' }}">
                <a href="{{ route('notifications.index') }}" aria-expanded="false" class="{{ request()->routeIs('notifications.*') ? 'mm-active' : '' }}">
                    <i class="fas fa-bell"></i>
                    <span class="nav-text">Notifications</span>
                    @php
                        $sideUnread = Auth::check() ? Auth::user()->unreadNotifications()->count() : 0;
                    @endphp
                    @if($sideUnread > 0)
                        <span class="badge badge-xs badge-danger ms-auto" style="font-size: 0.65rem; padding: 2px 7px; border-radius: 9999px; background-color: #ef4444;">{{ $sideUnread }}</span>
                    @endif
                </a>
            </li> -->
            @canany(['branch.view', 'roles.view', 'users.view'])
            <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                    <i class="fas fa-user"></i>
                    <span class="nav-text">Authentication</span>
                </a>
                <ul aria-expanded="false">
                    @can('branch.view')
                    <li><a href="/branch">Department</a></li>
                    @endcan
                    @can('roles.view')
                    <li><a href="/roles">Role</a></li>
                    @endcan
                    @can('users.view')
                    <li><a href="/user">Users</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany
            @can('customer.view')
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">Customers</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="/customers">Customer Directory</a></li>
                    <li><a href="{{ route('customer-tracking.index') }}">Customer Tracking</a></li>
                </ul>
            </li>
            @endcan

            @can('application.view')
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-chart-line"></i>
                    <span class="nav-text">Lease Applications</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="/application">Application</a></li>
                </ul>
            </li>
            @endcan


            @can('mining.view')
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-file-alt"></i>
                    <span class="nav-text">Mining Department</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="/miningplan">Services</a></li>
                    <li><a href="/projectfolder">Project Folders</a></li>
                    <li><a href="/process">Process</a></li>
                </ul>
            </li>
            @endcan
            @canany(['environment.view', 'ec_certificate.view', 'ec_compliance.view'])
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-leaf"></i>
                    <span class="nav-text">Environment Clearance</span>
                </a>
                <ul aria-expanded="false">
                    @can('environment.view')
                    <li><a href="{{ route('eviron.index') }}">All Applications</a></li>
                    @endcan
                    @can('environment.b2.create')
                    <!-- <li><a href="{{ route('eviron.create') }}">New Application</a></li> -->
                    @endcan
                    @can('ec_certificate.view')
                    <li><a href="{{ route('ec-certificate.index') }}">EC Certificate Issuance</a></li>
                    @endcan
                    @can('ec_compliance.view')
                    <li><a href="{{ route('ec-compliance.index') }}">Half Yearly Compliance</a></li>
                    @endcan
                </ul>
            </li>
            @endcanany
            @can('ppt.view')
            <li>
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="fas fa-landmark"></i>
                    <span class="nav-text">PPT Department</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('ppt-department.index') }}">Online Domain Process</a></li>
                </ul>
            </li>
            @endcan
            @can('dgps.view')
            <li><a href="{{ route('dgps-survey.index') }}" aria-expanded="false">
                    <i class="fa fa-map-marker-alt"></i>
                    <span class="nav-text">DGPS Survey</span>
                </a>
            </li>
            @endcan
            @can('drone.view')
            <li><a href="{{ route('drone-survey.index') }}" aria-expanded="false">
                    <i class="fa fa-paper-plane"></i>
                    <span class="nav-text">Drone Survey</span>
                </a>
            </li>
            @endcan

            {{-- Accounts & Financial Management Module --}}
            @canany(['account.view', 'account.create'])
            <li class="{{ request()->routeIs('accounts.*') ? 'mm-active' : '' }}">
                <a class="has-arrow {{ request()->routeIs('accounts.*') ? 'mm-active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('accounts.*') ? 'true' : 'false' }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span class="nav-text">Accounts</span>
                </a>
                <ul aria-expanded="{{ request()->routeIs('accounts.*') ? 'true' : 'false' }}" class="{{ request()->routeIs('accounts.*') ? 'mm-show' : '' }}">
                    @can('account.view')
                    <li class="{{ request()->routeIs('accounts.quotations.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('accounts.quotations.index') }}" class="{{ request()->routeIs('accounts.quotations.*') ? 'mm-active' : '' }}">Quotations</a>
                    </li>
                    @endcan

                    @can('account.create')
                    <li class="{{ request()->routeIs('accounts.payments.create') ? 'mm-active' : '' }}">
                        <a href="{{ route('accounts.payments.create') }}" class="{{ request()->routeIs('accounts.payments.create') ? 'mm-active' : '' }}">Collect Payment</a>
                    </li>
                    @endcan

                    @can('account.view')
                    <li class="{{ request()->routeIs('accounts.receipts.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('accounts.receipts.index') }}" class="{{ request()->routeIs('accounts.receipts.*') ? 'mm-active' : '' }}">Payment Receipts</a>
                    </li>
                    <li class="{{ request()->routeIs('accounts.ledger.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('accounts.ledger.index') }}" class="{{ request()->routeIs('accounts.ledger.*') ? 'mm-active' : '' }}">Customer Ledger</a>
                    </li>
                    <li class="{{ request()->routeIs('accounts.reports.*') ? 'mm-active' : '' }}">
                        <a href="{{ route('accounts.reports.index') }}" class="{{ request()->routeIs('accounts.reports.*') ? 'mm-active' : '' }}">Financial Reports</a>
                    </li>
                    @endcan
                </ul>
            </li>
            @endcanany

             {{--  <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-info-circle"></i>
                            <span class="nav-text">Apps</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="app-profile.html">Profile</a></li>
                            <li><a href="edit-profile.html">Edit Profile</a></li>
                            <li><a href="post-details.html">Post Details</a></li>
                            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">Email</a>
                                <ul aria-expanded="false">
                                    <li><a href="email-compose.html">Compose</a></li>
                                    <li><a href="email-inbox.html">Inbox</a></li>
                                    <li><a href="email-read.html">Read</a></li>
                                </ul>
                            </li>
                            <li><a href="app-calender.html">Calendar</a></li>
                            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">Shop</a>
                                <ul aria-expanded="false">
                                    <li><a href="ecom-product-grid.html">Product Grid</a></li>
                                    <li><a href="ecom-product-list.html">Product List</a></li>
                                    <li><a href="ecom-product-detail.html">Product Details</a></li>
                                    <li><a href="ecom-product-order.html">Order</a></li>
                                    <li><a href="ecom-checkout.html">Checkout</a></li>
                                    <li><a href="ecom-invoice.html">Invoice</a></li>
                                    <li><a href="ecom-customers.html">Customers</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-chart-line"></i>
                            <span class="nav-text">Charts</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="chart-flot.html">Flot</a></li>
                            <li><a href="chart-morris.html">Morris</a></li>
                            <li><a href="chart-chartjs.html">Chartjs</a></li>
                            <li><a href="chart-chartist.html">Chartist</a></li>
                            <li><a href="chart-sparkline.html">Sparkline</a></li>
                            <li><a href="chart-peity.html">Peity</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fab fa-bootstrap"></i>
                            <span class="nav-text">Bootstrap</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="ui-accordion.html">Accordion</a></li>
                            <li><a href="ui-alert.html">Alert</a></li>
                            <li><a href="ui-badge.html">Badge</a></li>
                            <li><a href="ui-button.html">Button</a></li>
                            <li><a href="ui-modal.html">Modal</a></li>
                            <li><a href="ui-button-group.html">Button Group</a></li>
                            <li><a href="ui-list-group.html">List Group</a></li>
                            <li><a href="ui-card.html">Cards</a></li>
                            <li><a href="ui-carousel.html">Carousel</a></li>
                            <li><a href="ui-dropdown.html">Dropdown</a></li>
                            <li><a href="ui-popover.html">Popover</a></li>
                            <li><a href="ui-progressbar.html">Progressbar</a></li>
                            <li><a href="ui-tab.html">Tab</a></li>
                            <li><a href="ui-typography.html">Typography</a></li>
                            <li><a href="ui-pagination.html">Pagination</a></li>
                            <li><a href="ui-grid.html">Grid</a></li>

                        </ul>
                    </li>
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-heart"></i>
                            <span class="nav-text">Plugins</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="uc-select2.html">Select 2</a></li>
                            <li><a href="uc-nestable.html">Nestedable</a></li>
                            <li><a href="uc-noui-slider.html">Noui Slider</a></li>
                            <li><a href="uc-sweetalert.html">Sweet Alert</a></li>
                            <li><a href="uc-toastr.html">Toastr</a></li>
                            <li><a href="map-jqvmap.html">Jqv Map</a></li>
                            <li><a href="uc-lightgallery.html">Light Gallery</a></li>
                        </ul>
                    </li>
                    <li><a href="widget-basic.html" aria-expanded="false">
                            <i class="fas fa-user"></i>
                            <span class="nav-text">Widget</span>
                        </a>
                    </li>
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-file-alt"></i>
                            <span class="nav-text">Forms</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="form-element.html">Form Elements</a></li>
                            <li><a href="form-wizard.html">Wizard</a></li>
                            <li><a href="form-ckeditor.html">CkEditor</a></li>
                            <li><a href="form-pickers.html">Pickers</a></li>
                            <li><a href="form-validation.html">Form Validate</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-table"></i>
                            <span class="nav-text">Table</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="table-bootstrap-basic.html">Bootstrap</a></li>
                            <li><a href="table-datatable-basic.html">Datatable</a></li>
                        </ul>
                    </li>
                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                            <i class="fas fa-clone"></i>
                            <span class="nav-text">Pages</span>
                        </a>
                        <ul aria-expanded="false">
                            <li><a href="page-login.html">Login</a></li>
                            <li><a href="page-register.html">Register</a></li>
                            <li><a class="has-arrow" href="javascript:void()" aria-expanded="false">Error</a>
                                <ul aria-expanded="false">
                                    <li><a href="page-error-400.html">Error 400</a></li>
                                    <li><a href="page-error-403.html">Error 403</a></li>
                                    <li><a href="page-error-404.html">Error 404</a></li>
                                    <li><a href="page-error-500.html">Error 500</a></li>
                                    <li><a href="page-error-503.html">Error 503</a></li>
                                </ul>
                            </li>
                            <li><a href="page-lock-screen.html">Lock Screen</a></li>
                            <li><a href="empty-page.html">Empty Page</a></li>
                        </ul>
                    </li> --}}
         </ul>
         {{-- <div class="side-bar-profile">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="side-bar-profile-img">
                            <img src="images/user.jpg" alt="">
                        </div>
                        <div class="profile-info1">
                            <h5>Levi Siregar</h5>
                            <span>leviregar@mail.com</span>
                        </div>

                    </div>


                </div> --}}

     </div>
 </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Collapsed sidebar submenu touch and click toggle
    const menuEl = document.getElementById('menu');
    if (!menuEl) return;

    menuEl.querySelectorAll(':scope > li').forEach(function(li) {
        const trigger = li.querySelector(':scope > a.has-arrow');
        const submenu = li.querySelector(':scope > ul');
        if (!trigger || !submenu) return;

        trigger.addEventListener('click', function(e) {
            const wrapper = document.getElementById('main-wrapper');
            if (wrapper && wrapper.classList.contains('menu-toggle')) {
                e.preventDefault();
                e.stopPropagation();

                // Close other open flyouts
                menuEl.querySelectorAll(':scope > li.flyout-open').forEach(function(otherLi) {
                    if (otherLi !== li) {
                        otherLi.classList.remove('flyout-open');
                    }
                });

                // Toggle this flyout
                li.classList.toggle('flyout-open');
            }
        });
    });

    // Close open flyout when clicking anywhere outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dlabnav')) {
            menuEl.querySelectorAll(':scope > li.flyout-open').forEach(function(li) {
                li.classList.remove('flyout-open');
            });
        }
    });
});
</script>

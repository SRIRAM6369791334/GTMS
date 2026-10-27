<!DOCTYPE html>
<html lang="en">

<head>
    <!-- PAGE TITLE HERE -->
    <title>@yield('title')</title>


    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Dexignlabs">
    <meta name="robots" content="index, follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">




    <!-- MOBILE SPECIFIC -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- FAVICONS ICON -->
    <link rel="shortcut icon" type="image/png" href="/images/gtmslogo.png">
    <link href="/vendor/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="/vendor/owl-carousel/owl.carousel.css" rel="stylesheet">
    <link rel="stylesheet" href="/vendor/nouislider/nouislider.min.css">

     <!-- Datatable -->
    <link href="/vendor/datatables/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="/vendor/datatables/responsive/responsive.css" rel="stylesheet">

     <!-- Toastr -->
    <link rel="stylesheet" href="/vendor/toastr/css/toastr.min.css">

     <link href="/vendor/sweetalert2/sweetalert2.min.css" rel="stylesheet">

    <!-- Style css -->
    <link href="/vendor/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="/css/style.css?v=2" rel="stylesheet">
    <link href="/css/dark-mode.css?v=3" rel="stylesheet">
    {{-- <link href="css/style1.css" rel="stylesheet"> --}}

    <style>
        /* GTMS Universal Pagination Styling */
        .pagination {
            display: inline-flex !important;
            align-items: center;
            margin-bottom: 0 !important;
            gap: 4px;
        }
        .pagination .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 6px !important;
            border: 1px solid #e2e8f0;
            color: #0F1E4D;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }
        .pagination .page-item.active .page-link {
            background-color: #0F1E4D !important;
            border-color: #0F1E4D !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25);
        }
        .pagination .page-item.disabled .page-link {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #94a3b8 !important;
            cursor: not-allowed;
            opacity: 0.7;
        }
        .pagination .page-item:not(.active):not(.disabled) .page-link:hover {
            background-color: #eff6ff !important;
            border-color: #0F1E4D !important;
            color: #0F1E4D !important;
        }
        /* Defend against unstyled SVG icons in pagination */
        .pagination svg, nav[role="navigation"] svg {
            width: 1rem !important;
            height: 1rem !important;
            max-width: 16px !important;
            max-height: 16px !important;
            display: inline-block !important;
        }

        /* GTMS DataTables Pagination Harmonization */
        .dataTables_wrapper .dataTables_paginate {
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            padding-top: 0.75rem !important;
            justify-content: flex-end !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button,
        .dataTables_wrapper .dataTables_paginate span .paginate_button,
        .dataTables_wrapper .dataTables_paginate .paginate_button.previous,
        .dataTables_wrapper .dataTables_paginate .paginate_button.next {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 34px !important;
            height: 34px !important;
            line-height: 34px !important;
            padding: 0 10px !important;
            margin: 0 2px !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
            border-radius: 6px !important;
            border: 1px solid #e2e8f0 !important;
            color: #0F1E4D !important;
            background: #ffffff !important;
            box-shadow: none !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
        }
        .dataTables_wrapper .dataTables_paginate span .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate span .paginate_button.current:hover {
            background: #0F1E4D !important;
            border-color: #0F1E4D !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(15, 30, 77, 0.25) !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:not(.current):not(.disabled):hover,
        .dataTables_wrapper .dataTables_paginate span .paginate_button:not(.current):hover {
            background: #eff6ff !important;
            border-color: #0F1E4D !important;
            color: #0F1E4D !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            opacity: 0.7 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button i {
            font-size: 0.8rem !important;
        }
        .dataTables_wrapper .dataTables_info {
            padding-top: 0.75rem !important;
            font-size: 0.85rem !important;
            color: #64748b !important;
        }

        /* Robust Sticky Footer & Layout Guard */
        #main-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content-body {
            flex: 1 0 auto;
        }
        .footer {
            clear: both;
            position: relative;
            z-index: 1;
            margin-top: auto;
            width: 100%;
        }

        /* Universal GTMS Tooltip Cleanliness & Single-Tooltip Guard */
        .tooltip {
            pointer-events: none !important;
        }
        .tooltip.show {
            z-index: 1090 !important;
        }

        /* ==========================================================================
           GTMS UNIFIED THEME POLISH (LEASE APPLICATION REFERENCE STANDARD)
           ========================================================================== */
        /* Primary Action Buttons */
        .btn-navy, .btn-app-primary {
            background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%) !important;
            color: #FFFFFF !important;
            border: none !important;
            border-radius: 9px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 12px rgba(15, 30, 77, 0.18) !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.5rem !important;
        }
        .btn-navy:hover, .btn-app-primary:hover {
            background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%) !important;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3) !important;
            transform: translateY(-1px) !important;
            color: #FFFFFF !important;
        }

        /* Action Buttons Group & Dock */
        .table-action-group, .app-action-dock {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 6px !important;
            white-space: nowrap !important;
        }
        .btn-action-icon, .btn-dock-icon {
            width: 32px !important;
            height: 32px !important;
            min-width: 32px !important;
            min-height: 32px !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 8px !important;
            font-size: 0.85rem !important;
            cursor: pointer !important;
            text-decoration: none !important;
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1) !important;
            flex-shrink: 0 !important;
            border: 1px solid transparent !important;
        }
        .btn-action-icon:hover, .btn-dock-icon:hover {
            transform: translateY(-1px) !important;
        }
        /* View: Soft Navy / Sky Blue */
        .btn-action-view, .btn-dock-view {
            background: #eff6ff !important;
            border-color: #bfdbfe !important;
            color: #1d4ed8 !important;
        }
        .btn-action-view:hover, .btn-dock-view:hover {
            background: #1d4ed8 !important;
            color: #ffffff !important;
        }
        /* Edit: Warm Ore-Gold */
        .btn-action-edit, .btn-dock-edit {
            background: #fef3c7 !important;
            border-color: #fde68a !important;
            color: #b45309 !important;
        }
        .btn-action-edit:hover, .btn-dock-edit:hover {
            background: #d97706 !important;
            color: #ffffff !important;
        }
        /* Delete: Soft Rose / Crimson */
        .btn-action-delete, .btn-dock-delete {
            background: #fee2e2 !important;
            border-color: #fecaca !important;
            color: #dc2626 !important;
        }
        .btn-action-delete:hover, .btn-dock-delete:hover {
            background: #dc2626 !important;
            color: #ffffff !important;
        }
        /* Download / Print */
        .btn-action-download, .btn-dock-download {
            background: #f0fdf4 !important;
            border-color: #bbf7d0 !important;
            color: #15803d !important;
        }
        .btn-action-download:hover, .btn-dock-download:hover {
            background: #15803d !important;
            color: #ffffff !important;
        }

        /* Universal GTMS Wizard & Lookup Card Tokens (Lease Application Standard) */
        .s1-lookup-card, .lookup-card-gtms {
            background: #f0f7ff !important;
            border: 1.5px solid #bfdbfe !important;
            border-radius: 12px !important;
            padding: 16px 18px !important;
            margin-bottom: 22px !important;
            transition: all 0.2s ease !important;
        }
        .s1-lookup-label {
            font-size: 0.88rem !important;
            color: #1e40af !important;
            line-height: 1.2 !important;
            font-weight: 700 !important;
        }
        .s1-lookup-badge {
            background: #1e40af !important;
            color: #ffffff !important;
            font-size: 0.72rem !important;
            font-weight: 600 !important;
            padding: 3px 8px !important;
            border-radius: 6px !important;
        }
        .s1-lookup-desc {
            font-size: 0.8rem !important;
            color: #475569 !important;
            line-height: 1.45 !important;
        }
        .s1-btn-fetch {
            background: #1e40af !important;
            color: #ffffff !important;
            font-size: 0.82rem !important;
            font-weight: 600 !important;
            border: 1.5px solid #1e40af !important;
            transition: all 0.15s ease !important;
        }
        .s1-btn-fetch:hover {
            background: #0f1e4d !important;
            color: #ffffff !important;
            border-color: #0f1e4d !important;
        }

        /* Dossier & Show View Alignment */
        .dossier-card, .card-dossier-gtms {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 14px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05) !important;
            transition: all 0.2s ease !important;
        }
        .dossier-card-header {
            background: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 1.1rem 1.4rem !important;
            border-top-left-radius: 13px !important;
            border-top-right-radius: 13px !important;
        }

        /* Modernized Modal & Dialog Styling */
        .modal-content {
            border-radius: 16px !important;
            border: 1px solid #E2E8F0 !important;
            box-shadow: 0 20px 40px rgba(15, 30, 77, 0.15) !important;
            overflow: hidden !important;
        }
        .modal-header {
            background: #F8FAFC !important;
            border-bottom: 1px solid #E2E8F0 !important;
            padding: 1.1rem 1.5rem !important;
        }
        .modal-title {
            font-size: 1.05rem !important;
            font-weight: 700 !important;
            color: #0F172A !important;
        }
        .modal-body {
            padding: 1.5rem !important;
        }
        .modal-footer {
            background: #F8FAFC !important;
            border-top: 1px solid #E2E8F0 !important;
            padding: 0.9rem 1.5rem !important;
        }
        .modal .btn-primary {
            background: linear-gradient(135deg, #0F1E4D 0%, #1E3A8A 100%) !important;
            border-color: #0F1E4D !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2) !important;
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .modal .btn-primary:hover {
            background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 100%) !important;
            border-color: #1E3A8A !important;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3) !important;
            transform: translateY(-1px) !important;
            color: #ffffff !important;
        }
        .modal .btn-secondary, .modal .btn-light {
            border-radius: 8px !important;
            font-weight: 600 !important;
            border: 1px solid #CBD5E1 !important;
            color: #475569 !important;
        }
        .modal .form-control, .modal .form-select {
            border-radius: 8px !important;
            border: 1px solid #CBD5E1 !important;
            font-size: 0.88rem !important;
        }
        .modal .form-control:focus, .modal .form-select:focus {
            border-color: #0F1E4D !important;
            box-shadow: 0 0 0 3px rgba(15, 30, 77, 0.12) !important;
        }

        /* Modernized SweetAlert2 Styling */
        .swal2-popup {
            border-radius: 16px !important;
            box-shadow: 0 20px 45px rgba(15, 30, 77, 0.18) !important;
            border: 1px solid #E2E8F0 !important;
            font-family: inherit !important;
            padding: 1.75rem !important;
        }
        .swal2-title {
            color: #0F172A !important;
            font-weight: 700 !important;
            font-size: 1.25rem !important;
            margin-bottom: 0.5rem !important;
        }
        .swal2-html-container {
            color: #475569 !important;
            font-size: 0.9rem !important;
            line-height: 1.5 !important;
        }
        .swal2-actions {
            margin-top: 1.5rem !important;
            gap: 0.5rem !important;
        }
        .swal2-actions button.swal2-confirm {
            background: #DC2626 !important;
            color: #FFFFFF !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            padding: 0.6rem 1.25rem !important;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25) !important;
        }
        .swal2-actions button.swal2-cancel {
            background: #0F1E4D !important;
            color: #FFFFFF !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
            padding: 0.6rem 1.25rem !important;
            box-shadow: 0 4px 12px rgba(15, 30, 77, 0.2) !important;
        }
    </style>
    @yield('styles')
    @stack('styles')
</head>

<body data-theme-version="{{ request()->cookie('version', 'light') }}">
    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
        <div class="lds-ripple">
            <div></div>
            <div></div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

        <!--**********************************
            Nav header start
        ***********************************-->
     @include('layouts.header')
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
        @include('layouts.sidebar')
        <!--**********************************
            Sidebar end
        ***********************************-->


        <!--**********************************
            Content body start
        ***********************************-->
          @yield('main_content')
        <!--**********************************
            Content body end
        ***********************************-->
        <!-- Modal -->
        <div class="modal fade" id="sendMessageModal">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Send Message</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form class="comment-form">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="text-black font-w600 form-label required">Name </label>
                                        <input type="text" class="form-control" value="Author" name="Author"
                                            placeholder="Author">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="text-black font-w600 form-label">Email </label>
                                        <input type="text" class="form-control" value="Email"
                                            placeholder="Email" name="Email">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label class="text-black font-w600 form-label">Comment</label>
                                        <textarea rows="8" class="form-control" name="comment" placeholder="Comment"></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="mb-3 mb-0">
                                        <input type="submit" value="Post Comment" class="submit btn btn-primary"
                                            name="submit">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!--**********************************
            Footer start
        ***********************************-->
          @include('layouts.footer')
        <!--**********************************
            Footer end
        ***********************************-->

        <!-- GTMS Universal Open With Modal -->
        @include('layouts.partials.open_with_modal')

        <!--**********************************
           Support ticket button start
        ***********************************-->

        <!--**********************************
           Support ticket button end
        ***********************************-->


    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="/vendor/global/global.min.js"></script>


    <!-- Toastr -->
    <script src="/vendor/toastr/js/toastr.min.js"></script>
    <!-- All init script -->
    <script src="/js/plugins-init/toastr-init.js"></script>

      <script src="/vendor/sweetalert2/sweetalert2.min.js"></script>
    <script src="/js/plugins-init/sweetalert.init.js"></script>
    <script src="/vendor/bootstrap-select/js/bootstrap-select.min.js"></script>


    <!-- counter -->
    <script src="/vendor/counter/counter.min.js"></script>
    <script src="/vendor/counter/waypoint.min.js"></script>

    <!-- Apex Chart -->
    <script src="/vendor/apexchart/apexchart.js"></script>
    <script src="/vendor/chart-js/chart.bundle.min.js"></script>
    <!-- Chart peity plugin files -->
    <script src="/vendor/peity/jquery.peity.min.js"></script>
    <!-- Dashboard 1 -->
    @if(request()->is('/') || request()->is('dashboard*'))
    <script src="/js/dashboard/dashboard-1.js?v=3"></script>
    @endif
     <!-- Datatable -->
    <script src="/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="/vendor/datatables/responsive/responsive.js"></script>
    <script src="/js/plugins-init/datatables.init.js"></script>



    <script src="/vendor/owl-carousel/owl.carousel.js"></script>

    <script src="/js/custom.min.js"></script>
    <script src="/js/app.js?v=3"></script>
    <script src="/js/admin.js"></script>
    <script src="/js/dlabnav-init.js"></script>
    <script>
        function cardsCenter() {
            /*  testimonial one function by = owl.carousel.js */
            jQuery('.card-slider').owlCarousel({
                loop: true,
                margin: 0,
                nav: true,
                //center:true,
                slideSpeed: 3000,
                paginationSpeed: 3000,
                dots: true,
                navText: ['<i class="fas fa-arrow-left"></i>', '<i class="fas fa-arrow-right"></i>'],
                responsive: {
                    0: {
                        items: 1
                    },
                    576: {
                        items: 1
                    },
                    800: {
                        items: 1
                    },
                    991: {
                        items: 1
                    },
                    1200: {
                        items: 1
                    },
                    1600: {
                        items: 1
                    }
                }
            })
        }

        jQuery(window).on('load', function() {
            setTimeout(function() {
                cardsCenter();
            }, 1000);
        });
    </script>


    <!-- GTMS Intelligent Open With Dispatcher -->
    <script src="/js/gtms-open-with.js?v=1"></script>

 @yield('scripts')
 @stack('scripts')

</body>

</html>

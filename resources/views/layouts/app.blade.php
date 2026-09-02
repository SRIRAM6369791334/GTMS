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
    <link href="/css/style.css?v=2" rel="stylesheet">
    {{-- <link href="css/style1.css" rel="stylesheet"> --}}

</head>

<body>
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
    <script src="vendor/toastr/js/toastr.min.js"></script>
    <!-- All init script -->
    <script src="/js/plugins-init/toastr-init.js"></script>

      <script src="/vendor/sweetalert2/sweetalert2.min.js"></script>
    <script src="/js/plugins-init/sweetalert.init.js"></script>
    <script src="/vendor/bootstrap-select/js/bootstrap-select.min.js"></script>


    <!-- counter -->
    <script src="/vendor/counter/counter.min.js"></script>
    <script src="vendor/counter/waypoint.min.js"></script>

    <!-- Apex Chart -->
    <script src="/vendor/apexchart/apexchart.js"></script>
    <script src="/vendor/chart-js/chart.bundle.min.js"></script>
    <!-- Chart peity plugin files -->
    <script src="/vendor/peity/jquery.peity.min.js"></script>
    <!-- Dashboard 1 -->
    <script src="/js/dashboard/dashboard-1.js"></script>
     <!-- Datatable -->
    <script src="/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="/vendor/datatables/responsive/responsive.js"></script>
    <script src="/js/plugins-init/datatables.init.js"></script>



    <script src="/vendor/owl-carousel/owl.carousel.js"></script>

    <script src="/js/custom.min.js"></script>
    <script src="/js/app.js?v=2"></script>
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


 @yield('scripts')

</body>

</html>

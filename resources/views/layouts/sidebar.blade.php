 <div class="dlabnav">
     <div class="dlabnav-scroll">
         <ul class="metismenu" id="menu">

             <li><a href="/dashboard" aria-expanded="false">
                     <i class="fas fa-home"></i>
                     <span class="nav-text">Home</span>
                 </a>
             </li>
             <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
                     <i class="fas fa-user"></i>
                     <span class="nav-text">Authentication</span>
                 </a>
                 <ul aria-expanded="false">
                     <li><a href="/branch">Department</a></li>
                     <li><a href="/roles">Role</a></li>
                     <li><a href="/user">Users</a></li>

                 </ul>

             </li>
             <li>
                 <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                     <i class="fas fa-chart-line"></i>
                     <span class="nav-text">Lease Applications</span>
                 </a>
                 <ul aria-expanded="false">
                     <li><a href="/application">Application</a></li>


                 </ul>
             </li>
             <li>
                 <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                     <i class="fas fa-file-alt"></i>
                     <span class="nav-text">Mining Plan</span>
                 </a>
                 <ul aria-expanded="false">
                     <li><a href="/miningplan">Mining Plan</a></li>
                     <li><a href="/projectfolder">Project Folders</a></li>
                     <li><a href="/process">Process</a></li>


                 </ul>
             </li>



             <li>
                 <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                     <i class="fas fa-info-circle"></i>
                     <span class="nav-text">Environment Clearance</span>
                 </a>
                 <ul aria-expanded="false">
                     <li><a href="/environstage1">Sub Category 1</a></li>
                     <li><a href="/environstage2">Sub Category 2</a></li>
                     <li><a href="{{ route('environment-b2.index') }}">B2 Document Process</a></li>
                     <li><a href="{{ route('ec-certificate.index') }}">EC Certificate Issuance</a></li>

                 </ul>
             </li>
             <li>
                 <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                     <i class="fas fa-landmark"></i>
                     <span class="nav-text">PPT Department</span>
                 </a>
                 <ul aria-expanded="false">
                     <li><a href="{{ route('ppt-department.index') }}">Online Domain Process</a></li>

                 </ul>
             </li>
             <li><a href="{{ route('dgps-survey.index') }}" aria-expanded="false">
                     <i class="fa fa-map-marker-alt fa-2x"></i>
                     <span class="nav-text">DGPS Survey</span>
                 </a>
             </li>
             <li><a href="{{ route('drone-survey.index') }}" aria-expanded="false">
                     <i class="fa fa-paper-plane fa-2x"></i>
                     <span class="nav-text">Drone Survey</span>
                 </a>
             </li>
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

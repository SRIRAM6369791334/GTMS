@extends('layouts.app')
@section('title', 'Mining Portal')
@section('main_content')

    <div class="content-body default-height">
        <div class="container-fluid">

            <div class="row page-titles">
                <div class="col-lg-6">
                    <ol class="breadcrumb">
                        {{-- <li class="breadcrumb-item"><a href="javascript:void(0)">Table</a></li> --}}
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Mining Plan</a></li>
                    </ol>
                </div>
                <div class="col-lg-6 text-end">
                    <a href="/newapplication"><button class="btn btn-rounded btn-info"><span
                                class="btn-icon-start text-info"><i class="fa fa-plus color-info"></i>
                            </span>New Application</button></a>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-3 col-xxl-3 col-lg-3 col-sm-6">
                    <div class="widget-stat card">
                        <div class="card-body p-4">
                            <div class="media ai-icon">
                                <span class="me-3 bgl-primary text-primary">
                                    <!-- <i class="ti-user"></i> -->
                                    <svg id="icon-customers" xmlns="http://www.w3.org/2000/svg" width="30"
                                        height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-user">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                                <div class="media-body">
                                    <p class="mb-1">Active applications</p>
                                    <h4 class="mb-0">3280</h4>
                                    {{-- <span class="badge badge-primary">+3.5%</span> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-xxl-3 col-lg-3 col-sm-6">
                    <div class="widget-stat card">
                        <div class="card-body p-4">
                            <div class="media ai-icon">
                                <span class="me-3 bgl-warning text-warning">
                                    <svg id="icon-orders" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                        <polyline points="14 2 14 8 20 8"></polyline>
                                        <line x1="16" y1="13" x2="8" y2="13"></line>
                                        <line x1="16" y1="17" x2="8" y2="17"></line>
                                        <polyline points="10 9 9 9 8 9"></polyline>
                                    </svg>
                                </span>
                                <div class="media-body">
                                    <p class="mb-1">Pending validation</p>
                                    <h4 class="mb-0">2570</h4>
                                    {{-- <span class="badge badge-warning">+3.5%</span> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-xxl-3 col-lg-3 col-sm-6">
                    <div class="widget-stat card">
                        <div class="card-body  p-4">
                            <div class="media ai-icon">
                                <span class="me-3 bgl-danger text-danger">
                                    <svg id="icon-revenue" xmlns="http://www.w3.org/2000/svg" width="30" height="30"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign">
                                        <line x1="12" y1="1" x2="12" y2="23"></line>
                                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                    </svg>
                                </span>
                                <div class="media-body">
                                    <p class="mb-1">Approved & verified</p>
                                    <h4 class="mb-0">364</h4>
                                    {{-- <span class="badge badge-danger">-3.5%</span> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-xxl-3 col-lg-3 col-sm-6">
                    <div class="widget-stat card">
                        <div class="card-body p-4">
                            <div class="media ai-icon">
                                <span class="me-3 bgl-success text-success">
                                    <svg id="icon-database-widget" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="feather feather-database">
                                        <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                        <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                                    </svg>
                                </span>
                                <div class="media-body">
                                    <p class="mb-1">Archived & backed up</p>
                                    <h4 class="mb-0">36</h4>
                                    {{-- <span class="badge badge-success">-3.5%</span> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- row -->


            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-end">


                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example10" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>S No</th>
                                            <th>Client</th>
                                            <th>District</th>
                                            <th>Mineral</th>
                                            <th>Plan type</th>
                                            <th>Stage</th>

                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>

                                            <td>Sri Bala Traders</td>
                                            <td>Salem</td>
                                            <td>Granite</td>
                                            <td>Mining Plan</td>
                                            <td><a href="#" class="badge badge-primary">Pending</a></td>

                                            <td>
                                                <a href="/viewapplication" class="btn btn-sm btn-primary">View</a>
                                                <a href="#" class="btn btn-sm btn-success">Approve</a>
                                                <a href="#" class="btn btn-sm btn-danger">Reject</a>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

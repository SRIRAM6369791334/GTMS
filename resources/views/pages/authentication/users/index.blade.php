@extends('layouts.app')
@section('title', 'Users')
@section('main_content')
<link href="css/style1.css" rel="stylesheet">
    <div class="content-body default-height">
        <div class="container-fluid">
            <div class="row page-titles">
                <div class="col-lg-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active"><a href="javascript:void(0)">Users</a></li>
                    </ol>
                </div>
                <div class="col-lg-6 text-end"><button class="btn btn-rounded btn-info" data-bs-toggle="modal"
                        data-bs-target=".bd-user-modal-lg"><span class="btn-icon-start text-info"><i
                                class="fa fa-plus color-info"></i></span>Add User</button></div>
            </div>
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-sm-6">
                    <div class="widget-stat card">
                        <div class="card-body p-4">
                            <div class="media ai-icon"><span class="me-3 bgl-primary text-primary"><i
                                        class="fa fa-users fa-2x"></i></span>
                                <div class="media-body">
                                    <p class="mb-1">Total Users</p>
                                    <h4 class="mb-0">{{ $users->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-sm-6">
                    <div class="widget-stat card">
                        <div class="card-body p-4">
                            <div class="media ai-icon"><span class="me-3 bgl-success text-success"><i
                                        class="fa fa-user-check fa-2x"></i></span>
                                <div class="media-body">
                                    <p class="mb-1">Active Users</p>
                                    <h4 class="mb-0">{{ $users->where('status', 1)->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-sm-6">
                    <div class="widget-stat card">
                        <div class="card-body p-4">
                            <div class="media ai-icon"><span class="me-3 bgl-warning text-warning"><i
                                        class="fa fa-user-clock fa-2x"></i></span>
                                <div class="media-body">
                                    <p class="mb-1">Inactive Users</p>
                                    <h4 class="mb-0">{{ $users->where('status', '!=', 1)->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-sm-6">
                    <div class="widget-stat card">
                        <div class="card-body p-4">
                            <div class="media ai-icon"><span class="me-3 bgl-danger text-danger"><i
                                        class="fa fa-user-tag fa-2x"></i></span>
                                <div class="media-body">
                                    <p class="mb-1">Roles Defined</p>
                                    <h4 class="mb-0">{{ $role->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="admin-panel mb-4">
                <div class="panel-head">
                    <div>
                        <h5>Team Members</h5>
                        <p class="sub">Manage access across Sub Category 1 &amp; 2 folders</p>
                    </div>
                    <button class="btn btn-sm"
                        style="background:var(--c-documents); color:#fff; font-weight:700; font-size:.8rem;"
                        data-bs-toggle="modal" data-bs-target=".bd-user-modal-lg"><i class="bi bi-plus-lg me-1"></i>Add
                        User</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-admin align-middle mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Folder Access</th>
                                <th>Status</th>
                                <th>Last Active</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2"><span class="av avatar-chip .av"
                                            style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#3350c9,#7a52a8);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.72rem;">RK</span>
                                        <div>
                                            <div class="fw-bold" style="font-size:.85rem;">R. Kannan</div>
                                            <div class="text-muted" style="font-size:.72rem;">kannan@ecportal.gov.in</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge-status uploaded"><i class="bi bi-shield-check"></i> Compliance
                                        Officer</span></td>
                                <td>Sub Category 1 &amp; 2</td>
                                <td><span class="badge-status approved"><i class="bi bi-circle-fill"
                                            style="font-size:.5rem;"></i> Active</span></td>
                                <td>2 min ago</td>
                                <td class="text-end"><button class="row-action-btn"><i class="fa fa-pencil"></i></button>
                                    <button class="row-action-btn"><i class="fa fa-list-ul"></i></button></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2"><span
                                            style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#1f8a55,#12968a);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.72rem;">SP</span>
                                        <div>
                                            <div class="fw-bold" style="font-size:.85rem;">S. Priya</div>
                                            <div class="text-muted" style="font-size:.72rem;">priya@ecportal.gov.in</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge-status uploaded"><i class="bi bi-file-earmark-text"></i>
                                        Documentation Lead</span></td>
                                <td>Sub Category 1</td>
                                <td><span class="badge-status approved"><i class="bi bi-circle-fill"
                                            style="font-size:.5rem;"></i> Active</span></td>
                                <td>1 hour ago</td>
                                <td class="text-end"><button class="row-action-btn"><i class="fa fa-pencil"></i></button>
                                    <button class="row-action-btn"><i class="fa fa-list-ul"></i></button></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2"><span
                                            style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#d97e1e,#c22568);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.72rem;">DM</span>
                                        <div>
                                            <div class="fw-bold" style="font-size:.85rem;">Dr. Meena</div>
                                            <div class="text-muted" style="font-size:.72rem;">meena@eiaexperts.in</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge-status uploaded"><i class="bi bi-flower1"></i> EIA Expert</span>
                                </td>
                                <td>Sub Category 2 &middot; Baseline Study</td>
                                <td><span class="badge-status approved"><i class="bi bi-circle-fill"
                                            style="font-size:.5rem;"></i> Active</span></td>
                                <td>3 hours ago</td>
                                <td class="text-end"><button class="row-action-btn"><i class="fa fa-pencil"></i></button>
                                    <button class="row-action-btn"><i class="fa fa-list-ul"></i></button></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2"><span
                                            style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#6b7280,#8b95c9);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.72rem;">AV</span>
                                        <div>
                                            <div class="fw-bold" style="font-size:.85rem;">A. Vignesh</div>
                                            <div class="text-muted" style="font-size:.72rem;">vignesh@ecportal.gov.in
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge-status pending"><i class="bi bi-eye"></i> Viewer</span></td>
                                <td>Sub Category 2</td>
                                <td><span class="badge-status rejected"><i class="bi bi-circle-fill"
                                            style="font-size:.5rem;"></i> Suspended</span></td>
                                <td>6 days ago</td>
                                <td class="text-end"><button class="row-action-btn"><i class="fa fa-pencil"></i></button>
                                    <button class="row-action-btn"><i class="fa fa-list-ul"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>



            </div>

              <div class="admin-panel">
        <div class="panel-head"><div><h5>Role Permissions</h5><p class="sub">What each role can see and do</p></div></div>
        <div class="table-responsive">
          <table class="table table-admin align-middle mb-0">
            <thead><tr><th>Role</th><th class="text-center">View Documents</th><th class="text-center">Upload</th><th class="text-center">Approve</th><th class="text-center">Manage Users</th></tr></thead>
            <tbody>
              <tr><td class="fw-bold">Compliance Officer</td><td class="text-center"><i class="fa fa-check-circle" style="color:var(--ok);"></i></td><td class="text-center"><i class="fa fa-check-circle" style="color:var(--ok);"></i></td><td class="text-center"><i class="fa fa-check-circle" style="color:var(--ok);"></i></td><td class="text-center"><i class="fa fa-check-circle" style="color:var(--ok);"></i></td></tr>
              <tr><td class="fw-bold">Documentation Lead</td><td class="text-center"><i class="fa fa-check-circle" style="color:var(--ok);"></i></td><td class="text-center"><i class="fa fa-check-circle" style="color:var(--ok);"></i></td><td class="text-center text-muted">—</td><td class="text-center text-muted">—</td></tr>
              <tr><td class="fw-bold">EIA Expert</td><td class="text-center"><i class="fa fa-check-circle" style="color:var(--ok);"></i></td><td class="text-center"><i class="fa fa-check-circle" style="color:var(--ok);"></i></td><td class="text-center text-muted">—</td><td class="text-center text-muted">—</td></tr>
              <tr><td class="fw-bold">Viewer</td><td class="text-center"><i class="fa fa-check-circle" style="color:var(--ok);"></i></td><td class="text-center text-muted">—</td><td class="text-center text-muted">—</td><td class="text-center text-muted">—</td></tr>
            </tbody>
          </table>
        </div>
      </div>
        </div>
    </div>
    @include('pages.authentication.users.createuser')
@endsection
@section('scripts')
<script src="{{ asset('js/ajax/user.js') }}"></script>@endsection

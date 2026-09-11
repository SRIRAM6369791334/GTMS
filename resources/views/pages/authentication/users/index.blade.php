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
                <div class="col-lg-6 text-end">
                    @can('users.create')
                    <button class="btn btn-rounded btn-info" data-bs-toggle="modal"
                        data-bs-target=".bd-user-modal-lg"><span class="btn-icon-start text-info"><i
                                class="fa fa-plus color-info"></i></span>Add User</button>
                    @endcan
                </div>

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
                        <p class="sub">Active users and their role assignments in GTMS</p>
                    </div>
                    @can('users.create')
                    <button class="btn btn-sm btn-info text-white fw-bold"
                        data-bs-toggle="modal" data-bs-target=".bd-user-modal-lg"><i class="fa fa-plus me-1"></i>Add User</button>
                    @endcan

                </div>
                <div class="table-responsive p-3">
                    <table id="example10" class="table table-hover align-middle mb-0 display" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Department</th>
                                <th>Mobile</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr id="user_row_{{ $user->id }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($user->image && file_exists(public_path('uploads/users/' . $user->image)))
                                                <img src="{{ asset('uploads/users/' . $user->image) }}" width="38" height="38" class="rounded-circle border" alt="">
                                            @else
                                                <span class="avatar-chip" style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#3350c9,#7a52a8);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem;">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </span>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark" style="font-size:.9rem;">{{ $user->name }}</div>
                                                <div class="text-muted" style="font-size:.75rem;">
                                                    <span class="badge bg-light text-muted border me-1">{{ $user->user_code ?? 'LUK_' . str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</span>
                                                    {{ $user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $assignedRole = $user->roles->first() ? $user->roles->first()->name : ($user->role ? $user->role->name : 'No Role');
                                        @endphp
                                        @if($assignedRole === 'Admin' || $assignedRole === 'Super Admin')
                                            <span class="badge bg-danger-subtle text-danger border border-danger"><i class="fa fa-shield-alt me-1"></i> {{ $assignedRole }}</span>
                                        @else
                                            <span class="badge bg-primary-subtle text-primary border border-primary"><i class="fa fa-user-tag me-1"></i> {{ $assignedRole }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->branch ? $user->branch->branch_name : 'General' }}</td>
                                    <td>{{ $user->mobile_num ?? '—' }}</td>
                                    <td>
                                        @if($user->status == 1)
                                            <span class="badge bg-success-subtle text-success border border-success"><i class="fa fa-check-circle me-1"></i> Active</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger"><i class="fa fa-times-circle me-1"></i> Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @can('users.edit')
                                        <button type="button" class="btn btn-primary edituserBtn shadow btn-xs sharp me-1"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->role_id }}"
                                            data-branch="{{ $user->branch_id }}"
                                            data-status="{{ $user->status }}"
                                            data-image="{{ $user->image }}"
                                            data-mobile="{{ $user->mobile_num }}"
                                            data-password="{{ $user->show_password }}">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        @endcan
                                        @can('users.delete')
                                        @if($user->id !== auth()->id() && !($user->hasRole('Admin') && $users->where('role_id', $user->role_id)->count() <= 1))
                                            <button type="button" class="btn btn-danger deleteuserBtn shadow btn-xs sharp me-1"
                                                data-id="{{ $user->id }}">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endif
                                        @endcan
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Role Permissions Matrix Overview -->
            <div class="admin-panel">
                <div class="panel-head">
                    <div>
                        <h5>Role Permissions Overview</h5>
                        <p class="sub">Assigned capabilities per role in the system</p>
                    </div>
                </div>
                <div class="table-responsive p-3">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th class="text-center">Total Permissions</th>
                                <th class="text-center">Users & Roles</th>
                                <th class="text-center">Mining Plans</th>
                                <th class="text-center">Environment B1/B2</th>
                                <th class="text-center">Surveys (DGPS/Drone)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($role as $r)
                                <tr>
                                    <td class="fw-bold text-dark">
                                        {{ $r->name }}
                                        @if($r->name === 'Admin')
                                            <span class="badge bg-danger ms-1">Full Access</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-primary border">{{ $r->name === 'Admin' ? 'All (Unrestricted)' : $r->permissions->count() . ' Permissions' }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($r->name === 'Admin' || $r->hasPermissionTo('users.view'))
                                            <i class="fa fa-check-circle text-success fs-16"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($r->name === 'Admin' || $r->hasPermissionTo('mining.view'))
                                            <i class="fa fa-check-circle text-success fs-16"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($r->name === 'Admin' || $r->hasPermissionTo('environment.view'))
                                            <i class="fa fa-check-circle text-success fs-16"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($r->name === 'Admin' || $r->hasPermissionTo('dgps.view') || $r->hasPermissionTo('drone.view'))
                                            <i class="fa fa-check-circle text-success fs-16"></i>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
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

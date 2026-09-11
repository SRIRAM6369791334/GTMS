@extends('layouts.app')
@section('title', 'Role')
@section('main_content')

    <div class="content-body default-height">
        <div class="container-fluid">

            <div class="row page-titles">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Table</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Role Table</a></li>
                </ol>
            </div>
            <!-- row -->


            <div class="row">



                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Roles</h4>

                            @can('roles.create')
                            <button type="button" class="btn btn-rounded btn-info" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg">
                                <span class="btn-icon-start text-info"><i class="fa fa-plus color-info"></i></span>Add
                            </button>
                            @endcan

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example10" class="display table" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>S No</th>
                                            <th>Role</th>
                                            <th>Permissions Assigned</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($roles as $role)
                                            <tr id="row{{ $role->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <span class="fw-bold text-dark fs-14">{{ $role->name }}</span>
                                                    @if($role->name === 'Admin' || $role->name === 'Super Admin')
                                                        <span class="badge badge-xs bg-danger ms-1">Super Admin</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($role->name === 'Admin')
                                                        <span class="badge bg-success-subtle text-success border border-success px-2 py-1">
                                                            <i class="fa fa-infinity me-1"></i> All Permissions (Super Admin)
                                                        </span>
                                                    @else
                                                        <span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1 me-1">
                                                            <i class="fa fa-lock me-1"></i> {{ $role->permissions->count() }} Permissions
                                                        </span>
                                                        <small class="text-muted d-block mt-1">
                                                            {{ Str::limit($role->permissions->pluck('name')->implode(', '), 50, '...') }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @can('roles.edit')
                                                    <button type="button" class="btn btn-primary editBtn shadow btn-xs sharp me-1" 
                                                        data-id="{{ $role->id }}" 
                                                        data-name="{{ $role->name }}"
                                                        title="Edit Role & Permissions">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>
                                                    @endcan

                                                    @can('roles.delete')
                                                    @if($role->name !== 'Admin' && $role->name !== 'Super Admin')
                                                        <button type="button" class="btn btn-danger deleteBtn shadow btn-xs sharp me-1"
                                                            data-id="{{ $role->id }}"
                                                            title="Delete Role">
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
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('pages.authentication.roles.createrole')


@endsection

@section('scripts')
    <script src="{{ asset('js/ajax/role.js') }}"></script>


@endsection

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

                            <button class="btn btn-rounded btn-info"><span class="btn-icon-start text-info"
                                    data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg"><i
                                        class="fa fa-plus color-info"></i>
                                </span>Add</button>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example10" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>S No</th>
                                            <th>Role</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($roles as $role)
                                            <tr id="row{{ $role->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $role->name }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary editBtn shadow btn-xs sharp me-1"  data-bs-toggle="modal" data-bs-target=".bd-edit-modal-lg"
                                                        data-id="{{ $role->id }}" data-name="{{ $role->name }}">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-danger deleteBtn shadow btn-xs sharp me-1"
                                                        data-id="{{ $role->id }}">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
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

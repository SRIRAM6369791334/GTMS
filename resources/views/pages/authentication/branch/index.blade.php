@extends('layouts.app')
@section('title', 'Branch')
@section('main_content')

    <div class="content-body default-height">
        <div class="container-fluid">

            <div class="row page-titles">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Table</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Branch Table</a></li>
                </ol>
            </div>
            <!-- row -->


            <div class="row">



                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Departments</h4>

                            <button class="btn btn-rounded btn-info"><span class="btn-icon-start text-info"
                                   data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg"><i
                                        class="fa fa-plus color-info"></i>
                                </span>Add Department</button>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example10" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>S No</th>
                                            <th>Department Name</th>
                                            <th>Contact Person</th>
                                            <th>Mobile</th>
                                            <th>Address</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($branches as $branch)
                                            <tr id="row{{ $branch->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $branch->branch_name }}</td>
                                                <td>{{ $branch->contact_person }}</td>
                                                <td>{{ $branch->mobile }}</td>
                                                <td>{{ $branch->address }}<br>{{ $branch->city }}, {{ $branch->state }} {{ $branch->pincode }}</td>
                                                <td>
                                                    @if ($branch->status == 1)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>


                                                    <button type="button" class="btn btn-primary shadow btn-xs sharp me-1 editbranchBtn"  data-bs-toggle="modal" data-bs-target=".bd-editbranch-modal-lg"
                                                        data-id="{{ $branch->id }}" data-name="{{ $branch->branch_name }}" data-contact="{{ $branch->contact_person }}" data-mobile="{{ $branch->mobile }}" data-address="{{ $branch->address }}"
                                                        data-city="{{ $branch->city }}" data-state="{{ $branch->state }}"
                                                        data-pincode="{{ $branch->pincode }}" data-status="{{ $branch->status }}">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-danger shadow btn-xs sharp me-1 deletebranchBtn"
                                                        data-id="{{ $branch->id }}">
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

    @include('pages.authentication.branch.creatbranch')


@endsection

@section('scripts')
    <script src="{{ asset('js/ajax/branch.js') }}"></script>
@endsection

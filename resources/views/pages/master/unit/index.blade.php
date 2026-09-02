@extends('layouts.app')
@section('title', 'Category')
@section('main_content')

    <div class="content-body default-height">
        <div class="container-fluid">

            <div class="row page-titles">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Table</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Unit Table</a></li>
                </ol>
            </div>
            <!-- row -->


            <div class="row">



                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Units</h4>

                            <button class="btn btn-rounded btn-info"><span class="btn-icon-start text-info"
                                    data-bs-toggle="modal" data-bs-target=".bd-unit-modal-lg"><i
                                        class="fa fa-plus color-info"></i>
                                </span>Add Units</button>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example10" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>S No</th>

                                            <th>Unit</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($units as $unit)
                                            <tr id="row{{ $unit->id }}">
                                                <td>{{ $loop->iteration }}</td>

                                                <td>{{ $unit->units }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary editunitBtn shadow btn-xs sharp me-1"  data-bs-toggle="modal" data-bs-target=".bd-editunit-modal-lg"
                                                        data-id="{{ $unit->id }}"

                                                        data-name="{{ $unit->units }}">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-danger deleteunitBtn shadow btn-xs sharp me-1"
                                                        data-id="{{ $unit->id }}">
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

    @include('pages.master.unit.createunit')


@endsection

@section('scripts')
    <script src="{{ asset('js/ajax/unit.js') }}"></script>


@endsection

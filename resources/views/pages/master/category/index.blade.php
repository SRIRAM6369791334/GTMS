@extends('layouts.app')
@section('title', 'Category')
@section('main_content')

    <div class="content-body default-height">
        <div class="container-fluid">

            <div class="row page-titles">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Table</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Category Table</a></li>
                </ol>
            </div>
            <!-- row -->


            <div class="row">



                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Categories</h4>

                            <button class="btn btn-rounded btn-info"><span class="btn-icon-start text-info"
                                    data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg"><i
                                        class="fa fa-plus color-info"></i>
                                </span>Add Category</button>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example10" class="display" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>S No</th>
                                            <th>Category Code</th>
                                            <th>Category</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($category as $cat)
                                            <tr id="row{{ $cat->id }}">
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $cat->cat_code }}</td>
                                                <td>{{ $cat->cat_name }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-primary editcategoryBtn shadow btn-xs sharp me-1"  data-bs-toggle="modal" data-bs-target=".bd-editcat-modal-lg"
                                                        data-id="{{ $cat->id }}"
                                                        data-code="{{ $cat->cat_code }}"
                                                        data-name="{{ $cat->cat_name }}">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-danger deletecategoryBtn shadow btn-xs sharp me-1"
                                                        data-id="{{ $cat->id }}">
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

    @include('pages.master.category.createcat')


@endsection

@section('scripts')
    <script src="{{ asset('js/ajax/category.js') }}"></script>


@endsection

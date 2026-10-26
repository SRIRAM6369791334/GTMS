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

                            @can('category.create')
                            <button type="button" class="btn btn-navy" data-bs-toggle="modal" data-bs-target=".bd-example-modal-lg">
                                <i class="fa fa-plus me-1"></i> Add Category
                            </button>
                            @endcan

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
                                                    <div class="table-action-group">
                                                        @can('category.edit')
                                                        <button type="button" class="btn-action-icon btn-action-edit editcategoryBtn" data-bs-toggle="modal" data-bs-target=".bd-editcat-modal-lg"
                                                            data-id="{{ $cat->id }}"
                                                            data-code="{{ $cat->cat_code }}"
                                                            data-name="{{ $cat->cat_name }}"
                                                            title="Edit">
                                                            <i class="fa fa-pencil"></i>
                                                        </button>
                                                        @endcan

                                                        @can('category.delete')
                                                        <button type="button" class="btn-action-icon btn-action-delete deletecategoryBtn"
                                                            data-id="{{ $cat->id }}"
                                                            title="Delete">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                        @endcan
                                                    </div>
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

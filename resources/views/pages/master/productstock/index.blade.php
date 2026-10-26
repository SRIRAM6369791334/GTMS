@extends('layouts.app')
@section('title', 'Products')
@section('main_content')

    <div class="content-body default-height">
        <div class="container-fluid">

            <div class="row page-titles">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Table</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Product Stock</a></li>
                </ol>
            </div>
            <!-- row -->


            <div class="row">


                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Product Stock</h4>
                            @can('product.create')
                            <button type="button" class="btn btn-navy" data-bs-toggle="modal" data-bs-target=".bd-product-modal-lg">
                                <i class="fa fa-plus me-1"></i> Add Product
                            </button>
                            @endcan
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example3" class="display" style="min-width: 845px">
                                    <thead>
                                        <tr>

                                            <th>S.No</th>
                                            <th>Bar Code</th>
                                            <th>Name</th>
                                            <th>Total Stock</th>
                                            <th>Available Stock</th>
                                            <th>Sales Stock</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($productstock as $key => $item)

                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $item->product->bar_code }}</td>
                                                <td>{{ $item->product->pro_name }}</td>
                                                <td>{{ $item->total_stock }}</td>
                                                <td>{{ $item->available_stock }}</td>
                                                <td>{{ $item->sale_stock }}</td>
                                                <td>
                                                    <div class="table-action-group">
                                                        @can('product.edit')
                                                        <a href="#" class="btn-action-icon btn-action-edit" title="Edit"><i
                                                                class="fas fa-pencil-alt"></i></a>
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



@endsection

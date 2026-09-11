@extends('layouts.app')
@section('title', 'Products')
@section('main_content')



    <div class="content-body default-height">
        <div class="container-fluid">

            <div class="row page-titles">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Table</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Product</a></li>
                </ol>
            </div>
            <!-- row -->


            <div class="row">


                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Products</h4>
                            @can('product.create')
                            <button class="btn btn-rounded btn-info"><span class="btn-icon-start text-info"
                                    data-bs-toggle="modal" data-bs-target=".bd-product-modal-lg"><i
                                        class="fa fa-plus color-info"></i>
                                </span>Add Product</button>
                            @endcan
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="example3" class="display" style="min-width: 845px">
                                    <thead>
                                        <tr>

                                            <th>S.No</th>
                                            <th>Branch</th>
                                            <th>Bar Code</th>
                                            <th>Name</th>
                                            <th>GST</th>
                                            <th>Cost %</th>
                                            <th>MRP</th>
                                            <th>Unit</th>
                                            <th>Qty</th>
                                            <th>DY</th>
                                            <th>DG</th>
                                            <th>DR</th>
                                            <th>Cat_Code</th>
                                            <th>Cat_Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($product as $product)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $product->branch ? $product->branch->branch_name : '' }}</td>
                                                <td>{{ $product->bar_code }}</td>
                                                <td>{{ $product->pro_name }}</td>
                                                <td>{{ $product->gst }}</td>
                                                <td>{{ $product->cast_per }}</td>
                                                <td>{{ $product->mrp }}</td>
                                                <td>{{ $product->unit }}</td>
                                                <td>{{ $product->qty }}</td>
                                                <td>{{ $product->discount_1 }}</td>
                                                <td>{{ $product->discount_2 }}</td>
                                                <td>{{ $product->discount_3 }}</td>
                                                <td>{{ $product->category ? $product->category->cat_code : '' }}
                                                </td>
                                                <td>{{ $product->category ? $product->category->cat_name : '' }}
                                                </td>

                                                <td>
                                                    <div class="d-flex">
                                                        @can('product.edit')
                                                        <a href="#" class="btn btn-primary shadow btn-xs sharp me-1" data-id="{{ $product->id }}" data-branch="{{ $product->branch_id }}" data-cat="{{ $product->cat_id }}" data-name="{{ $product->pro_name }}" data-gst="{{ $product->gst }}" data-cast-per="{{ $product->cast_per }}" data-mrp="{{ $product->mrp }}" data-unit="{{ $product->unit }}" data-qty="{{ $product->qty }}" data-discount-1="{{ $product->discount_1 }}" data-discount-2="{{ $product->discount_2 }}" data-discount-3="{{ $product->discount_3 }}"><i
                                                                class="fas fa-pencil-alt"></i></a>
                                                        @endcan
                                                        @can('product.delete')
                                                        <a href="#" data-id="{{ $product->id }}" class="btn btn-danger shadow btn-xs sharp"><i
                                                                class="fa fa-trash"></i></a>
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


 @include('pages.master.product.createproduct')


@endsection

@section('scripts')
    <script src="{{ asset('js/ajax/product.js') }}"></script>


@endsection

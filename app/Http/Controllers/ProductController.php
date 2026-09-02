<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Branch;
use App\Models\ProductStock;

class ProductController extends Controller
{
    public function index()
    {
        $product = Product::with(['category', 'branch'])->where('delete_status', 0)->get();
        $category = Category::all();
        $branch = Branch::all();

        return view('pages.master.product.index', compact('product', 'category', 'branch'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'cat_id' => 'required',
            'pro_name' => 'required',
            'gst' => 'required',
            'cast_per' => 'required',
            'mrp' => 'required',
            'qty' => 'required',
        ]);

        $cast =  $request->cast_per;
        $mrps = $request->mrp;
        $value = $mrps / 100;
        $amount_data = $value * $cast;
        $amount = $mrps - $amount_data;

        if($cast == ''){
           $amounts = $request->mrp;
        }
        else{
            $amounts  = $amount;
        }


      $product = new Product();
$product->branch_id = $request->branch_id;
$product->cat_id = $request->cat_id;
$product->pro_name = $request->pro_name;
$product->gst = $request->gst;
$product->cast_per = $amount;
$product->mrp = $amounts;
$product->unit = 'Nos';
$product->qty = $request->qty;
$product->discount_1 = $request->discount_1;
$product->discount_2 = $request->discount_2;
$product->discount_3 = $request->discount_3;

// First save to generate the ID
$product->save();

// Now ID is available
$product->bar_code = 'PRO_' . str_pad($product->id, 3, '0', STR_PAD_LEFT);

// Save again to update the barcode
$product->save();

    $product_stock = new ProductStock();
    $total = $product_stock->total_stock;
    $product_stock->product_id = $product->id;
    $product_stock->total_stock =  $total + $request->qty;
    $product_stock->available_stock = $request->qty;
    $product_stock->sale_stock = 0;

    $product_stock->save();



     $product->load(['category','branch']);

       return response()->json([
            'status' => 1,
            'message' => 'Product Created Successfully',
            'data' => $product
        ]);
    }
}

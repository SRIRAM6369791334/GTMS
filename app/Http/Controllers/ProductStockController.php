<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductStock;

class ProductStockController extends Controller
{
    public function index()
    {
       $productstock = ProductStock::with(['product'])->get();


        return view('pages.master.productstock.index', compact('productstock'));
    }
}

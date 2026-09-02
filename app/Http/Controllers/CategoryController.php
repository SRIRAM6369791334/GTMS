<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::all();
        return view('pages.master.category.index', compact('category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cat_code' => 'required|unique:categories,cat_code',
            'cat_name' => 'required|unique:categories,cat_name',
        ]);

        $category = new Category();
        $category->cat_code = $request->input('cat_code');
        $category->cat_name = $request->input('cat_name');
        $category->save();

        return response()->json([
              'status'=>1,
        'message'=>'Category Added Successfully',
        'data'=>$category
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'cat_code' => 'required|unique:categories,cat_code,' . $request->input('id'),
            'cat_name' => 'required|unique:categories,cat_name,' . $request->input('id'),
        ]);

        $category = Category::find($request->input('id'));
        if (!$category) {
            return response()->json([
                'status' => 0,
                'message' => 'Category not found',
            ]);
        }

        $category->cat_code = $request->input('cat_code');
        $category->cat_name = $request->input('cat_name');
        $category->save();

        return response()->json([
            'status' => 1,
            'message' => 'Category Updated Successfully',
            'data' => $category
        ]);
    }

    public function destroy(Request $request)
    {
        $category = Category::find($request->input('id'));
        if (!$category) {
            return response()->json([
                'status' => 0,
                'message' => 'Category not found',
            ]);
        }

        $category->delete_status = 1;

        $category->save();

        return response()->json([
            'status' => 1,
            'message' => 'Category Deleted Successfully',
        ]);
    }
}

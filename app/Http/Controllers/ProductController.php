<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index');
    }

    public function create()
    {
        return view('products.create');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'category_id' => 'required|integer',
            'units' => 'required|array',
            'units.*' => 'required',
            'code' => 'required|string|max:255|unique:products,code,' . $product->id,
        ]);

        $product->update($request->only(['name', 'description', 'category_id', 'code']));

        $product->units()->sync($request->units);

        return response()->json(['data' => $product], 200);

    }
}

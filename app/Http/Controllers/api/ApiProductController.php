<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiProductController extends Controller
{

    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'category' => 'required|integer',
            'units' => 'required|array',
            'units.*' => 'integer',
            'code' => 'required|string|max:255|unique:products',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'code' => $request->code,
            'category_id' => $request->category,
        ]);

        // Attach units to the product
        $product->units()->attach($request->units);

        return response()->json(['data' => $product], 201);
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

//        foreach ($product->units as $unit) {
//            $product->units()->detach($unit->id);
//        }
//        foreach ($request->units as $unit) {
//            $product->units()->attach($unit);
//        }
        $product->units()->sync($request->units);

        return response()->json($product->load(['category', 'units']));
    }

    public function show($id)
    {

    }

    public function getUnits($id)
    {
        return Product::findOrFail($id)->units;
    }

    public function addQuantity(Request $request,$id)
    {

    }

    public function changePrice(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric',
            'productId' => 'required',
            'unitId' => 'required',
            'expiryDate' => 'required|date'
        ]);
        $product = Product::findOrFail($id);
       if(auth()->user()->roles->contains('name', 'administrator')){
           $stock = \DB::table('product_unit')->where('product_id', $request->productId)->where('unit_id', $request->unitId)->first();
           if ($stock) {
               $product->units()->updateExistingPivot($request->unitId, ['price' => $request->price, 'expiry_date' => $request->expiryDate]);
               return response()->json(['message' => 'Price updated successfully'], 200);
           } else {
               return response()->json(['message' => 'Product not found'], 404);
           }
        }else{
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
    }

    public function getExpiry($productId, $unitId)
    {
        $expiry = \DB::table('product_unit')->where('product_id', $productId)->where('unit_id', $unitId)->first()->expiry_date;
        return \response()->json(['expiry' => $expiry]);
    }

}

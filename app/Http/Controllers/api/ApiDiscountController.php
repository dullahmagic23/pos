<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discount;
use Illuminate\Support\Facades\DB;

class ApiDiscountController extends Controller
{
    public function index()
    {
        $discounts = Discount::all();
        return response()->json($discounts);
    }



    public function update(Request $request, $id)
    {
        $discount = Discount::find($id);
        $discount->update($request->all());
        return response()->json($discount);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'code' => 'required',
            'discount_type_id' => 'required',
            'value' => 'required',
            'status' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ]);
        $discount = Discount::create([
            'name' => $request->name,
            'code' => $request->code,
            'discount_type_id' => $request->discount_type_id,
            'value' => $request->value,
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);
        return response()->json($discount);
    }

    public function destroy($id)
    {
        $discount = Discount::find($id);
        $discount->delete();
        return response()->json($discount);
    }

    public function applyToProduct(Request $request, $id)
    {
        $check = DB::table('discount_product')->where('discount_id', $request->discount_id)->where('product_id', $id)->first();
        if ($check != null) {
            return response()->json(['message' => 'Discount already applied to this product']);
        }
        $applied = DB::table('discount_product')->insert([
            'discount_id' => $request->discount_id,
            'product_id' => $id,
        ]);
        return response()->json(['message' => 'Discount applied to product']);
    }

    public function removeToProduct($productId, $discountId)
    {
        $check = DB::table('discount_product')->where('discount_id', $discountId)->where('product_id', $productId)->first();
        if ($check == null) {
            return response()->json(['message' => 'Discount not applied to this product']);
        }
        $removed = DB::table('discount_product')->where('discount_id', $discountId)->where('product_id', $productId)->delete();
        return response()->json(['message' => 'Discount removed from product']);
    }
}

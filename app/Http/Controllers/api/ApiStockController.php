<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Conversion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiStockController extends Controller
{
    public function index()
    {
        return Stock::all();
    }


    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'quantity' => 'required',
            'unit_id' => 'required',
            'expiry_date' => 'required',
            'added_date' => 'required',
        ]);
        $store = Store::where('product_id', $request->product_id)->where('unit_id', $request->unit_id)->first();
        $stock = DB::table('product_unit')->where('product_id', $request->product_id)->where('unit_id', $request->unit_id)->first();
        if ($request->quantity > $store->quantity) {
            return response()->json(['message' => "Stock quantity is less than the quantity you want to add, you can add only up to " . $store->quantity], 400);
        } else {
            if ($stock != null) {
                DB::table('product_unit')->where('product_id', $request->product_id)->where('unit_id', $request->unit_id)->update([
                    'quantity' => $stock->quantity + $request->quantity,
                    'price' => $store->selling_price,
                    'expiry_date' => $request->expiry_date,
                    'added_date' => $request->added_date,
                ]);
                $store->update([
                    'quantity' => $store->quantity - $request->quantity,
                ]);
                Shop::create([
                    'product_id' => $request->product_id,
                    'unit_id' => $request->unit_id,
                    'quantity' => $request->quantity,
                    'selling_price' => $store->selling_price,
                    'date' => $request->added_date,
                    'expiry_date' => $request->expiry_date,
                    'user_id' => auth()->id(),
                ]);
            } else {
                DB::table('product_unit')->insert([
                    'product_id' => $request->product_id,
                    'unit_id' => $request->unit_id,
                    'price' => $store->selling_price,
                    'quantity' => $request->quantity,
                    'expiry_date' => $request->expiry_date,
                    'added_date' => $request->added_date,
                ]);
                $store->update([
                    'quantity' => $store->quantity - $request->quantity,
                ]);
                Shop::create([
                    'product_id' => $request->product_id,
                    'unit_id' => $request->unit_id,
                    'quantity' => $request->quantity,
                    'selling_price' => $store->selling_price,
                    'date' => $request->added_date,
                    'expiry_date' => $request->expiry_date,
                    'user_id' => auth()->id(),
                ]);
            }
        }
    }

    public function addQuantity(Request $request)
    {
        $request->validate([
            'quantity' => 'required',
        ]);
        $stock = Stock::find($request->stock);
        $quantity = $stock->quantity + $request->quantity;
        $stock->update([
            'quantity' => $quantity,
        ]);

        Store::create([
            'stock_id' => $stock->id,
            'quantity' => $request->quantity,
        ]);
        return response()->json(['message' => 'Stock quantity updated successfully'], 200);
    }

    public function find($id)
    {
        return Stock::find($id);
    }

    public function convert(Request $request)
    {

        $request->validate([
            'product_id' => 'required',
            'unitFrom' => 'required',
            'unitTo' => 'required',
            'stockAmount' => 'required',
            'conversionRate' => 'required',
        ]);
        $from_unit = DB::table('product_unit')->where('product_id', $request->product_id)->where('unit_id', $request->unitFrom)->first();
        $to_unit = DB::table('product_unit')->where('product_id', $request->product_id)->where('unit_id', $request->unitTo)->first();
        if($request->unitTo === $request->unitFrom){
            return response()->json(['message' => 'You cannot convert the same unit'], 400);
        }
        if ($from_unit == null) {
            return response()->json(['message' => 'The product does not have the selected unit'], 400);
        } else {
            if ($from_unit->quantity < $request->quantity) {
                return response()->json(['message' => 'The quantity you want to convert is more than the available quantity'], 400);
            } else {
                $result = $request->stockAmount * $request->conversionRate;
                if ($to_unit != null) {
                    DB::table('product_unit')->where('product_id', $request->product_id)->where('unit_id', $request->unitTo)->update([
                        'quantity' => $to_unit->quantity + $result,
                    ]);
                    DB::table('product_unit')->where('product_id', $request->product_id)->where('unit_id', $request->unitFrom)->update([
                        'quantity' => $from_unit->quantity - $request->stockAmount,
                    ]);
                } else {
                    DB::table('product_unit')->insert([
                        'product_id' => $request->product_id,
                        'unit_id' => $request->unitTo,
                        'quantity' => $result,
                        'price' => $from_unit->price,
                    ]);
                    DB::table('product_unit')->where('product_id', $request->product_id)->where('unit_id', $request->from_unit_id)->update([
                        'quantity' => $from_unit->quantity - $request->quantity,
                    ]);
                }

                Conversion::create([
                    'product_id' => $request->product_id,
                    'from_unit_id' => $request->unitFrom,
                    'to_unit_id' => $request->unitTo,
                    'quantity' => $request->stockAmount,
                    'conversionRate' => $request->conversionRate,
                    'result' => $result,
                    'ratio' => $request->conversionRate,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
                return response()->json(['message' => 'Conversion successful'], 200);
            }
        }

    }
}

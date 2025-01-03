<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiInventoryController extends Controller
{

    public function index()
    {
        return Inventory::latest()->get();
    }
    public function store(Request $request)
    {
        $inventory = new Inventory();
        $inventory->uuid = Str::uuid();
        $inventory->date = $request->date;
        $inventory->user_id = auth()->id();
        $inventory->supplier_id = $request->supplier_id;
        $inventory->save();

        foreach ($request->items as $item) {
            Order::create([
                'product_id' => $item['product_id'],
                'inventory_id' => $inventory->id,
                'supplier_id' => $request->supplier_id,
                'quantity' => $item['quantity'],
                'unit_id' => $item['unit_id'],
                'user_id' => auth()->id(),
                'buying_price' => $item['buying_price'],
                'selling_price' => $item['selling_price'],
                'uuid' => $inventory->uuid,
            ]);

            $stores = Store::where(['product_id' => $item['product_id'],'unit_id'=>$item['unit_id']])->get();
            if ($stores->count() > 0) {
                $store = $stores->first();
                $store->quantity = $store->quantity + $item['quantity'];
                $store->buying_price = $item['buying_price'];
                $store->selling_price = $item['selling_price'];
                $store->save();
            } else {
                Store::create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'buying_price' => $item['buying_price'],
                    'selling_price' => $item['selling_price'],
                    'user_id' => auth()->id(),
                ]);
            }
        }
        //update the stores

        return response()->json(['message' => 'Inventory created successfully'], 201);
    }

    public function show($id)
    {
        return Inventory::find($id);
    }

    public function all()
    {
        return Store::all();
    }
}

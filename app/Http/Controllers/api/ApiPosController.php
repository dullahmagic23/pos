<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\Return_;

class ApiPosController extends Controller
{
    public function index()
    {
        return response()->json([
            'sale' => Sale::all()
        ]);
    }

    //getProductQuantity() Method

    public function getQuantity($product_id,$unit_id)
    {
        $stock = DB::table('product_unit')->where('product_id',$product_id)->where('unit_id',$unit_id)->first();
        return response()->json([
            'quantity' => $stock->quantity,
            'price' => $stock->price
        ]);
    }

    public function store(Request $request)
    {
        $uuid = Str::uuid();
        $sale = Sale::create([
            'uuid' => $uuid,
            'customer_id' => $request->customer_id,
            'user_id' => auth()->user()->id,
            'date' => $request->date
        ]);
        foreach ($request->items as $item) {
           DB::table('product_sale')->insert([
               'sale_id' => $sale->id,
               'product_id' => $item['product_id'],
               'unit_id' => $item['unit_id'],
               'quantity' => $item['quantity'],
               'price' => $item['price'],
                'total' => $item['total']
           ]);

            $stock = DB::table('product_unit')->where('product_id',$item['product_id'])->where('unit_id',$item['unit_id'])->first();
            DB::table('product_unit')->where('product_id',$item['product_id'])->where('unit_id',$item['unit_id'])->update([
                'quantity' => $stock->quantity - $item['quantity']
            ]);
        }

        return response()->json(['message' => 'Sales recorded successfully']);
    }

    public function sell()
    {

    }
}




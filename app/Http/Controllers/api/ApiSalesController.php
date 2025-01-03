<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiSalesController extends Controller
{
    public function index()
    {
        return Sale::with('invoice')->latest()->get();
    }

    public function cancel($id)
    {
        $sale = Sale::find($id);
       if (auth()->user()->roles->contains('name','administrator') &&  $sale->payments->sum('amount') == 0) {
        foreach ($sale->products as $product) {
               $stock = DB::table('product_unit')
                   ->where('product_id',$product->id)
                   ->where('unit_id',$product->pivot->unit_id)->first();
               DB::table('product_unit')
                   ->where('product_id',$product->id)
                   ->where('unit_id',$product->pivot->unit_id)->update([
                   'quantity' => $stock->quantity + $product->pivot->quantity
               ]);
           }
           $sale->delete();
           return response()->json(['message' => 'Sale cancelled successfully']);
       } else{
              return response()->json(['message' => 'You can not delete this sale, You dont have enough permissions or the sale has payments', 'status' => 403]); 
       }
    }

    public function find($id)
    {
        return Sale::with('invoice')->find($id);
    }

    public function total($id)
    {
        $sale = Sale::find($id);
        $amount = DB::table('product_sale')->where('sale_id',$id)->sum('total');
        return $amount;
    }
}

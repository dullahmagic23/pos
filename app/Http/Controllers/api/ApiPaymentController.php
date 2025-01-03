<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiPaymentController extends Controller
{

    public function index()
    {
        return Payment::all();
    }
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required',
            'method_id'=> 'required',
            'sale_id' => 'required',
            'date' => 'required',
            'status' => 'required',
        ]);
        $sale = Sale::find($request->sale_id);
        $amount = DB::table('product_sale')->where('sale_id', $request->sale_id)->get()->sum('total');
        if ($request->amount > ($amount-($sale->payments?$sale->payments->sum('amount'):0))) {
            return response()->json(['message' => 'Amount entered is greater than the amount required'], 400);
        } elseif ($request->amount < ($amount-($sale->payments?$sale->payments->sum('amount'):0))) {
            Payment::create([
                'amount' => $request->amount,
                'payment_method_id' => $request->method_id,
                'sale_id' => $request->sale_id,
                'customer_id' => $sale->customer_id,
                'payment_date' => $request->date,
                'payment_status' => 'partial',
                'user_id' => auth()->user()->id,
            ]);
            return response()->json(['message' => 'Payment is partial'], 200);
        } else {
            Payment::create([
                'amount' => $request->amount,
                'payment_method_id' => $request->method_id,
                'sale_id' => $request->sale_id,
                'customer_id' => $sale->customer_id,
                'payment_date' => $request->date,
                'payment_status' => 'full',
                'user_id' => auth()->user()->id,
            ]);

            return response()->json(['message' => 'Payment is full'], 200);
        }
    }
}

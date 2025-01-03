<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiExpenseController extends Controller
{
    public function index()
    {
        return Expense::all();
    }

    public function find(Expense $expense)
    {
        return $expense;
    }

    public function update(Expense $expense, Request $request)
    {

    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required',
            'paymentMethod' => 'required',
            'amount' => 'required',
            'description' => 'required',
            'vendor' => 'required',
            'date' => 'required',
            'notes' => 'required',
        ]);
        Expense::create([
           'expense_category_id' => $request->category,
           'payment_method_id' => $request->paymentMethod,
           'amount' =>  $request->amount,
           'description' => $request->description,
            'supplier' => $request->vendor,
            'date' => $request->date,
            'notes' => $request->notes,
            'user_id' => auth()->id()
        ]);

        return Response::HTTP_CREATED;
    }
}

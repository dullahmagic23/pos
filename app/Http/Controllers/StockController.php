<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        return view('stocks.index');
    }

    public function create()
    {
        return view('stocks.create');
    }

    public function addQuantity(Request $request)
    {
        return $request;
    }

    public function convert()
    {
        return view('stocks.convert');
    }
}

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class ApiPaymentMethodController extends Controller
{
    public function index()
    {
        return PaymentMethod::latest()->get();
    }

    public function getPaymentMethod($id)
    {
        return PaymentMethod::find($id);
    }

    public function store(Request $request)
    {
        return $request;

    }
}

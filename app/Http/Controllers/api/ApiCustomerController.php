<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiCustomerController extends Controller
{
    public function store(Request $request)
    {
        Customer::create([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'tin' => $request->tin,
        ]);
        return Response::HTTP_CREATED;
    }

    public function index()
    {
        return Customer::all();
    }

    public function update(Request $request, $id)
    {
        $user = Customer::find($id);
        $user->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'tin' => $request->tin,
        ]);
        return Response::HTTP_OK;
    }
}

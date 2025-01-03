<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiSupplierController extends Controller
{
    public function index()
    {
        return Supplier::all();
    }

    public function store(Request $request)
    {
       $validatedData = $request->validate([
           'name' => 'required',
           'tin' => 'required',
           'phone' => 'required',
           'address' => 'required',
       ]);

         $supplier = Supplier::create($validatedData);
         return response()->json(Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        $supplier->update($request->all());
        return response()->json(Response::HTTP_OK);
    }
}

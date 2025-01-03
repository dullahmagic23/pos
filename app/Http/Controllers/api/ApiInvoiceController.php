<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ApiInvoiceController extends Controller
{
    public function index()
    {
        return Invoice::with('sale')->latest()->get();
    }

    public function store(Request $request)
    {
        Invoice::create([
            'uuid' => Str::uuid(),
            'user_id' => auth()->id(),
            'sale_id' => $request->sale['id']
        ]);
        return Response::HTTP_CREATED;
    }

    public function delete()
    {
        
    }

    public function find($uuid)
    {
        return Invoice::where('uuid', $uuid)->first();
    }
}

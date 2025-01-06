<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiCompanyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required',
            'phone' => 'required',
            'tin' => 'required|max:12'
        ]);
        Company::create($request->all());
        return Response::HTTP_CREATED;
    }
}

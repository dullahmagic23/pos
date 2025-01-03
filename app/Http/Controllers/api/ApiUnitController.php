<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiUnitController extends Controller
{
    public function index()
    {
        return Unit::all();
    }

    public function store(Request $request)
    {
        Unit::create(['name' => $request->name]);
        return Response::HTTP_CREATED;
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::find($id);
        $unit->name = $request->name;
        $unit->save();
        return Response::HTTP_OK;
    }
}

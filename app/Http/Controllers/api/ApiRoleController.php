<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRoleController extends Controller
{
    public function index(){
        return Role::all();
    }

    public function store(Request $request)
    {
        Role::create($request->all());
        return response()->json([],Response::HTTP_CREATED);
    }

    public function update(Request $request)
    {
        $role = Role::find($request->id);
        $role->update($request->all());
        return response()->json([],Response::HTTP_OK);
    }
}

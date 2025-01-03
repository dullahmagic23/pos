<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiUserController extends Controller
{
    public function index(){
        return User::all();
    }

    public function update(Request $request)
    {

        // Fetch the user by ID
        $user = User::findOrFail($request->id);

        // Update user's basic details
        $user->name = $request->name;
        $user->email = $request->email;

        // Save the updated user
        $user->save();

        // Update roles (optional, based on provided input)
        $user->roles()->sync($request->role_ids);


        return response()->json([
            'message' => 'User details and roles updated successfully.',
            'user' => $user->load('roles'), // Includes the updated user with roles in the response
        ], Response::HTTP_OK);
    }
}

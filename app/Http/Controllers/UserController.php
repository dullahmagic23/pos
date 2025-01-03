<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{

    public function index(){
        $users = User::all();
        return view('users.index', compact('users'));
    }
    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request){
        if ($request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'confirmed:password_confirmation',
            'roles' => 'required',
        ])){
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            foreach ($request->roles as $role) {
                $user->roles()->attach($role);
            }

            return response()->json(['message' => 'User created successfully.','status' => Response::HTTP_CREATED]);
        } else{
            return response()->json(['message' => 'Error creating user.','status' => Response::HTTP_UNPROCESSABLE_ENTITY]);
        }

    }
}

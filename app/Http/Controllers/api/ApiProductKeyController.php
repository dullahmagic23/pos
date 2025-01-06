<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\ProductKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;

class ApiProductKeyController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'key' => ['required', 'regex:/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/'],
        ], [
            'key.required' => 'The product key is required.',
            'key.regex' => 'The product key must be in the format XXXX-XXXX-XXXX.',
        ]);

        $keys = ProductKey::all();
        foreach ($keys as $key){
            if ($validated['key'] === Crypt::decryptString($key->key)){
                $key->update(['is_active' => 1]);
                return response()->json(['message' => 'Valid Product key entered','status' => 200]);
            }
        }

        return response()->json(['message' => 'Invalid Product key entered','status' => 400]);
    }
}

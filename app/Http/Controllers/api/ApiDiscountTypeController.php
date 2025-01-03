<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiscountType;

class ApiDiscountTypeController extends Controller
{
    public function index()
    {
        $discountTypes = DiscountType::all();
        return response()->json($discountTypes);
    }
}

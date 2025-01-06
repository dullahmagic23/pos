<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductKeyController extends Controller
{
    public function index()
    {
        return view('enterProductKey');
    }
}

<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ApiExpenseCategoryController extends Controller
{
    public function index()
    {
        return ExpenseCategory::all();
    }
}

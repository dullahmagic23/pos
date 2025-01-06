<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckCompanyRegistration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if there is any company in the companies table
        $companyExists = DB::table('companies')->exists();

        if ($companyExists) {
            // If a company exists, redirect to the login page
            return $next($request);
        } else {
            // If no company exists, redirect to the page to create a company
            return redirect()->route('companies.create');
        }

        return  $next($request);
    }
}

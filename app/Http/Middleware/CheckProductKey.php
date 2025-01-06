<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class CheckProductKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if there is an active product key in the database
        $activeProductKeyExists = DB::table('product_keys')->where('is_active', 1)->exists();

        if (!$activeProductKeyExists) {
            // Redirect to the page to enter the product key
            return redirect()->route('enterProductKey');
        }

        // If an active product key exists, proceed with the request
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class CheckUsers
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
        // Check if there are any users in the database
        $userExists = DB::table('users')->exists();

        if (!$userExists) {
            // Redirect to the page to create a user
            return redirect()->route('users.create');
        }

        // If users exist, proceed with the request
        return $next($request);
    }
}

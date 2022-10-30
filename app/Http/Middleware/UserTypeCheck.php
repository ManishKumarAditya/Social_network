<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserTypeCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ... $roles)
    {
        $user = Auth::user();
        foreach($roles as $role) {
            // Check if user has the role This check will depend on how your roles are set up
            if($role == 'Customer'  && $user->profile_type == 'App\Models\Customer') {
                return $next($request);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}

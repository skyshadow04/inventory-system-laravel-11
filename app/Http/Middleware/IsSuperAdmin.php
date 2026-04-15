<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Super admin is identified by is_superadmin = true
        if ($request->user() && $request->user()->is_superadmin) {
            return $next($request);
        }

        return redirect('/users')->with('error', 'Unauthorized access. Super admin privileges required.');
    }
}

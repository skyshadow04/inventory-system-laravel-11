<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ($request->user()->is_superadmin || $request->user()->is_manager || $request->user()->is_resource_officer || $request->user()->is_store_watcher)) {
            if ($request->user()->is_superadmin) {
                return redirect('/superadmin');
            } elseif ($request->user()->is_manager) {
                return redirect('/manager'); // Assuming there's a manager dashboard
            } elseif ($request->user()->is_resource_officer) {
                return redirect('/resource-officer');
            } else {
                return redirect('/store-watcher');
            }
        }

        return $next($request);
    }
}

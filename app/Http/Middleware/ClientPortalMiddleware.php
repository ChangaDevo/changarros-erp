<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClientPortalMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isClient()) {
            return redirect()->route('portal.login');
        }
        return $next($request);
    }
}

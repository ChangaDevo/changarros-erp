<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->isAdmin() || !auth()->user()->isActivo()) {
            return redirect()->route('admin.login');
        }
        return $next($request);
    }
}

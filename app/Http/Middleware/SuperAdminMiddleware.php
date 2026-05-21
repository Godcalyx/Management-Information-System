<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle($request, Closure $next)
{
    if (!auth()->check() || auth()->user()->role !== 'super_admin') {
        abort(403);
    }
    return $next($request);
}
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckMaintenance
{
    public function handle(Request $request, Closure $next)
{
    $maintenance = config('app.maintenance.maintenance_mode', false);
    $isAuthenticated = auth()->check();
    $userRole = $isAuthenticated ? auth()->user()->role : 'not logged in';

    \Log::info("Maintenance Debug: maintenance=$maintenance, authenticated=$isAuthenticated, role=$userRole, blocking=" . ($maintenance && (!$isAuthenticated || $userRole !== 'admin') ? 'yes' : 'no'));

    if ($maintenance) {
        if (!$isAuthenticated || $userRole !== 'admin') {
            \Log::info("Blocking user: maintenance on, not admin");
            return response()->view('maintenance');
        }
    }

    return $next($request);
}
}

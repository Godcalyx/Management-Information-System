<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Skip if not logged in or if not required to change password
        if (!$user || !$user->must_change_password) {
            return $next($request);
        }

        // Allow access only to change-password routes
        if ($request->routeIs('password.change') || $request->routeIs('password.update')) {
            return $next($request);
        }

        // Redirect to the change password page
        return redirect()->route('password.change');
    }
}

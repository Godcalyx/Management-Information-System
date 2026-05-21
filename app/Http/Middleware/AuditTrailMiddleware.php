<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;

class AuditTrailMiddleware
{
    protected $ignoreRoutes = [
        '/', 'dashboard', 'heartbeat', // Add routes you don't want logged
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $user = Auth::user();
        if ($user && !in_array($request->path(), $this->ignoreRoutes)) {
            $data = $request->except(['password', 'password_confirmation', '_token']);

            AuditTrail::create([
                'user_id'    => $user->id,
                'role'       => $user->role ?? 'unknown',
                'action'     => $request->method() . ' ' . $request->path(),
                'details'    => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
                'ip_address' => $request->ip(),
            ]);
        }

        return $response;
    }
}

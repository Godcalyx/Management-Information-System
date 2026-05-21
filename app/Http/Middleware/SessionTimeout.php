<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
public function handle($request, Closure $next)
{
    $timeout = setting('security_session_timeout', 30) * 60;

    if (auth()->check()) {
        if (session()->has('lastActivity') &&
            time() - session('lastActivity') > $timeout) {

            auth()->logout();
            session()->invalidate();
            session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['session' => 'Session expired due to inactivity.']);
        }

        session(['lastActivity' => time()]);
    }

    return $next($request);
}

}

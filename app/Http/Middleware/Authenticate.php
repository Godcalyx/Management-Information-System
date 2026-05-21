<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Determine intended login route based on URL
        $loginRoute = null;
        if ($request->is('admin/*') || $request->is('login/admin')) {
            $loginRoute = 'login.admin';
        } elseif ($request->is('professor/*') || $request->is('login/professor')) {
            $loginRoute = 'login.professor';
        } elseif ($request->is('student/*') || $request->is('login/student')) {
            $loginRoute = 'login.student';
        }

        // If route exists, use it; otherwise, fallback to homepage
        if ($loginRoute && Route::has($loginRoute)) {
            return route($loginRoute);
        }

        return url('/welcome');
    }
}

<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

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
        if (! $request->expectsJson()) {

            if ($request->is('admin/*') || $request->is('login/admin')) {
                return route('login.admin');
            } elseif ($request->is('professor/*') || $request->is('login/professor')) {
                return route('login.professor');
            } else {
                // Default to student login
                return route('login.student');
            }
        }
    }
}

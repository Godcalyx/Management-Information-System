<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;

class LogUserLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event): void
    {
        // Attempt to get user from event first, fallback to any active guard
        $user = $event->user;

        if (!$user) {
            foreach (array_keys(config('auth.guards')) as $guard) {
                $user = Auth::guard($guard)->user();
                if ($user) break; // found an active user
            }
        }

        if (!$user) return; // no user to log

        AuditTrail::create([
            'user_id' => $user->id,
            'user_type' => $user->role ?? 'N/A',
            'action' => 'User Logged Out',
            'ip_address' => request()->ip(),
        ]);
    }
}

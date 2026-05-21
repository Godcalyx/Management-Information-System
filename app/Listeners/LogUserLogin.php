<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\AuditTrail;

class LogUserLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        AuditTrail::create([
            'user_id' => $event->user->id,
            'user_type' => $event->user->role,
            'action' => 'User Logged In',
            'details' => json_encode([
                'ip' => request()->ip(),
                'user_agent' => request()->header('User-Agent')
            ]),
            'ip_address' => request()->ip(),
        ]);
    }
}

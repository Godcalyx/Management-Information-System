<?php


use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{

public function boot()
{
    parent::boot();

    Event::listen(Login::class, function ($event) {
        AuditTrail::create([
            'user_id'    => $event->user->id,
            'role'       => $event->user->role ?? 'unknown',
            'action'     => 'login',
            'details'    => json_encode([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        ]);
    });

    Event::listen(Logout::class, function ($event) {
        AuditTrail::create([
            'user_id'    => $event->user->id,
            'role'       => $event->user->role ?? 'unknown',
            'action'     => 'logout',
            'details'    => json_encode(['ip' => request()->ip()], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
        ]);
    });
}

protected $listen = [
    \Illuminate\Auth\Events\Login::class => [
        \App\Listeners\LogUserLogin::class,
    ],
    
    \Illuminate\Auth\Events\Logout::class => [
        \App\Listeners\LogUserLogout::class,
    ],
];

}
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Announcement;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
{
    View::composer('*', function ($view) {
        $unreadCount = 0;

        if (auth()->check()) {
            $userId = auth()->id();
            $unreadCount = Announcement::whereDoesntHave('users', function ($q) use ($userId) {
                $q->where('user_id', $userId)->where('is_read', true);
            })->count();
        }

        $view->with('unreadAnnouncementCount', $unreadCount);
    });
}

    public function register()
    {
        //
    }
}

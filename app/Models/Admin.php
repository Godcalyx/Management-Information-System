<?php

namespace App\Models;

use App\Notifications\RoleAwareResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable
{
    use Notifiable;

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new RoleAwareResetPasswordNotification($token, 'admin'));
    }


    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
public function user()
    {
        return $this->belongsTo(User::class);
    }

}

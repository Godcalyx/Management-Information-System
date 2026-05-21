<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\RoleAwareResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'lrn',
        'role',
        'status',
        'grade_level',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function professor()
    {
        return $this->hasOne(Professor::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function enrollmentByLRN()
    {
        return $this->hasOne(Enrollment::class, 'lrn', 'lrn');
    }

    public function enrollmentByEmail()
    {
        return $this->hasOne(Enrollment::class, 'email', 'email');
    }

    public function announcements()
    {
        return $this->belongsToMany(Announcement::class)->withPivot('is_read')->withTimestamps();
    }

    public function grades()
{
    return $this->hasMany(\App\Models\Grade::class, 'user_id');
}


    public function assignedGradeLevels()
    {
        return $this->hasMany(ProfessorSubjectGradeLevel::class, 'user_id');
    }

    // In User.php model
    // public function enrollment()
    // {
    //     return $this->hasOne(Enrollment::class, 'user_id');
    // }
    public function enrollment()
    {
        return $this->hasOne(Enrollment::class)->latestOfMany();
    }


    // app/Models/User.php
    // User.php
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'user_id');
    }

    public function latestEnrollment()
    {
        return $this->hasOne(Enrollment::class)->latestOfMany();
    }




    public function assignedGrades()
    {
        return $this->hasMany(Announcement::class, 'user_id');
    }

    public function advisory()
    {
        return $this->hasOne(\App\Models\Advisory::class, 'user_id');
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (is_null($user->grade_level)) {
                $user->grade_level = 7;
            }
        });
    }

    public function getGradeLevelAttribute($value)
    {
        return $value ?? 7;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new RoleAwareResetPasswordNotification($token));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'lrn',
        // remove 'role' if using Spatie roles
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

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
        return $this->hasMany(Announcement::class);
    }

    public function grades()
{
    return $this->hasMany(Grade::class, 'user_id');
}


    public function assignedGradeLevels()
{
    return $this->hasMany(\App\Models\GradeLevelProfessor::class);
}

    // In User.php model
public function enrollment() {
    return $this->hasOne(Enrollment::class, 'user_id');
}

// app/Models/User.php

public function enrollments()
{
    return $this->hasMany(\App\Models\Enrollment::class);
}
public function assignedGrades()
{
    return $this->hasMany(Announcement::class, 'user_id');
}





}

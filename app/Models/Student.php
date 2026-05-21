<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Student extends Model
{
    public function user()
{
    return $this->belongsTo(User::class);
}

public function grades() {
    return $this->hasMany(Grade::class);
}
public function enrollments()
{
    return $this->hasMany(\App\Models\Enrollment::class, 'user_id', 'user_id');
}




}

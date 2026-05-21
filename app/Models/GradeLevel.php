<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model
{
    protected $fillable = [
        'name',
        'description',
        'order',
    ];

    // If you have an Enrollment model with grade_level_id FK
    public function enrollments()
    {
        return $this->hasMany(\App\Models\Enrollment::class, 'grade_level_id');
    }

    // Optional: subjects relationship if added later
    public function subjects()
    {
    return $this->belongsToMany(\App\Models\Subject::class, 'grade_level_subject')->orderBy('order');
    }
}

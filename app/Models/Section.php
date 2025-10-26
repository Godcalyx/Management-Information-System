<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    public function subjects()
{
    return $this->belongsToMany(Subject::class, 'professor_subject_section')
        ->withPivot('professor_id')
        ->withTimestamps();
}

    
    public function students() {
        return $this->hasMany(User::class); // if users have section_id
    }
    
}

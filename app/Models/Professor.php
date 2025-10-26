<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Professor extends Model
{
    public function user()
{
    return $this->belongsTo(User::class);
}
public function subjects()
{
    return $this->belongsToMany(Subject::class, 'professor_subject_section')
        ->withPivot('section_id')
        ->withTimestamps();
}


}

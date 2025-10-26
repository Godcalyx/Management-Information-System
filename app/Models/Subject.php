<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    public function professors()
{
    // return $this->belongsToMany(Professor::class, 'professor_subject_section')
    //     ->withPivot('section_id')
    //     ->withTimestamps();
}
// app/Models/Subject.php

public function grades()
{
    return $this->hasMany(\App\Models\Grade::class);
}


    protected $fillable = ['name', 'grade_level'];

        
}

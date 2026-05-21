<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'order',
        'grade_level',
        'version',
        'grade_level_id'
    ];

    // Many-to-many relationship to GradeLevel through pivot table
    public function gradeLevels()
    {
        return $this->belongsToMany(
            GradeLevel::class,       // Related model
            'grade_level_subject',   // Pivot table
            'subject_id',            // Foreign key on pivot table for this model
            'grade_level_id'         // Foreign key on pivot table for related model
        );
    }

    // One-to-many relationship with grades
    public function grades()
    {
        return $this->hasMany(\App\Models\Grade::class);
    }

    // Main grade level (one-to-one / belongsTo)
    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class, 'grade_level_id');
    }
}

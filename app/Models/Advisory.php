<?php

// App/Models/Advisory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advisory extends Model
{
    protected $fillable = ['user_id', 'grade_level_id', 'school_year'];

    // Relationship to GradeLevel
    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class, 'grade_level_id');
    }

    // Relationship to User (professor)
    public function professor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

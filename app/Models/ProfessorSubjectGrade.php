<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessorSubjectGrade extends Model
{
    protected $table = 'professor_subject_grade_level'; // 👈 specify the correct table name

    protected $fillable = [
        'user_id',
        'subject_id',
        'grade_level',
        // add any other columns you're saving through mass assignment
    ];
    public function subject()
{
    return $this->belongsTo(Subject::class, 'subject_id');
}

public function professor()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function students()
{
    return User::where('role', 'student')
               ->where('grade_level', $this->grade_level)
               ->get();
}


}

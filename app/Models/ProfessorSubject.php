<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfessorSubject extends Model
{
    use HasFactory;

    // Explicit table name
    protected $table = 'professor_subjects';

    protected $fillable = [
        'user_id',
        'subject_id',
        'school_year',
    ];

    // Relation to Subject
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    // Relation to Professor (User)
    public function professor()
    {
        return $this->belongsTo(User::class, 'user_id');    
    }
    public function gradeLevel()
{
    return $this->belongsTo(GradeLevel::class);
}

}

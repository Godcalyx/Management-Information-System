<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfessorSubjectGradeLevel extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'subject_id', 'grade_level'];

    /**
     * Relationship: Each record belongs to a professor (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Each record belongs to a specific subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}

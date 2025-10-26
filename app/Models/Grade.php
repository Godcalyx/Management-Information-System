<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_id',
        'section_id',
        'quarter',
        'school_year',
        'grade',
        'submitted_by',
        'approved_by',
        'status',
    ];

    // Relationships (optional but recommended)

    public function student()
{
    return $this->belongsTo(User::class, 'user_id')->where('role', 'student');
}


    public function subject() {
    return $this->belongsTo(Subject::class);
}



    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function user()
{
    return $this->belongsTo(User::class);
}




}

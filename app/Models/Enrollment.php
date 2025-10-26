<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'lrn','email', 'school_year', 'grade_level',
        'last_name', 'first_name', 'middle_name', 'extension_name',
        'birthdate', 'birthplace', 'sex', 'mother_tongue',
        'ip_community', 'ip_specify', 'is_4ps', 'household_id',
        'current_house', 'current_street', 'current_barangay', 'current_city', 'current_province', 'current_country', 'current_zip',
        'permanent_house', 'permanent_street', 'permanent_barangay', 'permanent_city', 'permanent_province', 'permanent_country', 'permanent_zip',
        'father_last', 'father_first', 'father_middle', 'father_contact',
        'mother_last', 'mother_first', 'mother_middle', 'mother_contact',
        'guardian_last', 'guardian_first', 'guardian_middle', 'guardian_contact',
        'modality', 'status', 'documents',
    ];

    protected $casts = [
        'modality' => 'array',
        'birthdate' => 'date',
        'documents' => 'array',
    ];

    public function student()
{
    return $this->belongsTo(User::class, 'student_id');
}



public function subjects()
{
    return $this->belongsToMany(Subject::class);
}
public function subject()
{
    return $this->belongsTo(Subject::class);
}
public function user() {
    return $this->belongsTo(User::class);
}

public function grades() {
    return $this->hasMany(Grade::class, 'user_id', 'user_id');
}


public function attendance() {
    return $this->hasMany(Attendance::class, 'enrollment_id', 'id');
}





}

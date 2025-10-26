<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'school_year',
        'month',
        'days_of_school',
        'days_present',
        'days_absent',
        'times_tardy',
        'encoded_by',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function encoder()
    {
        return $this->belongsTo(User::class, 'encoded_by');
    }
}

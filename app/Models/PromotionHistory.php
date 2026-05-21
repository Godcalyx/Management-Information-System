<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionHistory extends Model
{
    protected $table = 'promotion_history';

    protected $fillable = [
        'enrollment_id',
        'admin_id',
        'from_grade',
        'to_grade',
        'school_year',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
}

<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ReportCardRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'form_type', 'status'];
    protected $table = 'form_requests'; // ✅ Correct table name


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

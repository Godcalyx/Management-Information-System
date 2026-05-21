<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
    'code',
    'description',
    'category',
    'visibility',
    'file_path',
    'file_size',
    'file_type',
];


}

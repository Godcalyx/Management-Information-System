<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $fillable = ['user_id', 'role', 'action', 'details', 'ip_address'];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formattedDetails(): string
    {
        $data = $this->details;
        if (!is_array($data)) return $this->details;

        return collect($data)->map(function ($v, $k) {
            if (is_array($v)) return "$k: [complex]";
            return "$k: $v";
        })->implode(', ');
    }
}

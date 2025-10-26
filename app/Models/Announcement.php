<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'attachment', 'user_id'];

    // Get the attachment URL for the announcement
    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }
    public function user()
{
    return $this->belongsTo(User::class);
}

}

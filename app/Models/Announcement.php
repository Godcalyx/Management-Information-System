<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'attachment', 'user_id', 'target_grades'];

    // Get the attachment URL for the announcement
    public function getAttachmentUrlAttribute()
{
    if ($this->attachment) {
        return asset('storage/' . $this->attachment);
    }
    return null;
}
    public function users()
{
    return $this->belongsToMany(User::class)->withPivot('is_read')->withTimestamps();
}
public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}



}

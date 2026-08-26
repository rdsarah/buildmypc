<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'user_id',
        'parent_id',
        'content',
    ];

    // The blog this comment belongs to
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }

    // The user who wrote the comment
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Parent comment if this is a reply
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    // Replies to this comment
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
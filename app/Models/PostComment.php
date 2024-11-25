<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'post_comments';

    
    protected $fillable = [
        'resident_id',
        'post_report_id',
        'parent_id',
        'comment',
        'likes',
        'dislikes'
    ];

    public function resident()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(PostReport::class);
    }

    public function replies()
    {
        return $this->hasMany(PostComment::class, 'parent_id');
    }
}

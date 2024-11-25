<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'post_reports';

    protected $fillable = [
        'resident_id',
        'type',
        'address',
        'photo',
        'video_url',
        'description'
    ];

    // Relationship with resident (user)
    public function resident()
    {
        return $this->belongsTo(User::class);
    }

    // Fetch top-level comments (where parent_id is null)
    public function postcomments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function replies()
    {
        return $this->hasMany(PostComment::class, 'parent_id');
    }
}

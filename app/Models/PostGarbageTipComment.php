<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostGarbageTipComment extends Model
{
    use HasFactory;

    protected $table = 'post_garbagetip_comments';

    
    protected $fillable = [
        'user_id',
        'garbage_tip_id',
        'parent_id',
        'comment',
        'likes',
        'dislikes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post_garbagetip()
    {
        return $this->belongsTo(GarbageTip::class);
    }

    public function replies_garbagetip()
    {
        return $this->hasMany(PostGarbageTipComment::class, 'parent_id');
    }
}

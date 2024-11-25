<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GarbageTip extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'garbage_tips';

    protected $fillable = [
        'title',
        'photos',
        'video',
        'description',
        'report_count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post_garbagetip_comments()
    {
        return $this->hasMany(PostGarbageTipComment::class);
    }

    public function replies()
    {
        return $this->hasMany(PostGarbageTipComment::class, 'parent_id');
    }

    public function garbage_reports()
    {
        return $this->hasMany(ReportGarbageTip::class, 'garbage_tip_id');
    }

}

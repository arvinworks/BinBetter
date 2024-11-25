<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'location',
        'time',
        'capacity',
        'status',
    ];

    public function joinEvents()
    {
        return $this->hasMany(JoinEvent::class);
    }
}

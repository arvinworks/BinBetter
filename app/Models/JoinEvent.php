<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JoinEvent extends Model
{
    use HasFactory;

    protected $table = 'join_events';

    protected $fillable = [
        'user_id',
        'event_id',
        'generate_qr',
        'time_in',
        'time_out',
        'claimed_rewards',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

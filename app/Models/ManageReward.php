<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManageReward extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'manage_rewards';

    protected $fillable = [
        'reward_type',
        'reward_amount',
        'reward_expiration_value',
        'reward_expiration_type',
        'status',
    ];

}

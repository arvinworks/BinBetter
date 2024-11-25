<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionSettings extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'subscription_settings';

    protected $fillable = [
        'subscription_type',
        'subscription_desc',
        'subscription_reward'
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'subscription_sett_id');
    }

    public function rewards()
    {
        return $this->hasMany(ManageReward::class, 'reward_type', 'subscription_reward');
    }
}

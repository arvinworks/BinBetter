<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionExpiration extends Model
{
    use HasFactory;

    protected $table = 'subscription_expirations';

    protected $fillable = [
        'subscription_id',
        'expiration_date',
        'reward_dates'
    ];

    public function subscriptions()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimReward extends Model
{
    use HasFactory;

    protected $table = 'claim_rewards';

    protected $fillable = [
        'subs_expiry_id',
        'user_id',
        'date_claim',
        'amount_claim'
    ];

    public function subscription_expiration()
    {
        return $this->belongsTo(SubscriptionExpiration::class, 'subs_expiry_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

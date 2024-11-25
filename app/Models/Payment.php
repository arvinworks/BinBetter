<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Payment extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $table = 'payments';

    protected $fillable = [
        'user_id',
        'gcash_setting_id',
        'amount',
        'upload_proof_payment',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}

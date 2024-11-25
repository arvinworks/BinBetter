<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportGarbageTip extends Model
{
    use HasFactory;

    protected $table = 'report_garbagetips';

    protected $fillable = [
        'user_id',
        'garbage_tip_id',
        'report_type',
        'report_message',
        'report_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function garbagetip()
    {
        return $this->belongsTo(GarbageTip::class, 'garbage_tip_id');
    }

}

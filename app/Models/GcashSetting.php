<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GcashSetting extends Model
{
    use HasFactory;

    protected $table = 'gcash_settings';

    protected $fillable = [
        'gcash_number',
        'gcash_qr',
        'status'
    ];
}

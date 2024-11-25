<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GarbageSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'garbage_schedules';

    protected $fillable = [
        'street',
        'barangay',
        'time',
        'collection_day',
    ];
}

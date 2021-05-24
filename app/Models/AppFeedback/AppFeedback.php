<?php

namespace App\Models\AppFeedback;

use Illuminate\Database\Eloquent\Model;

class AppFeedback extends Model
{
    protected $table = 'app_feedback';
    protected $fillable = [
        'feedback',
        'device_id',
        'latitude',
        'longitude',
        'country',
        'city',
        'village'
    ];
}

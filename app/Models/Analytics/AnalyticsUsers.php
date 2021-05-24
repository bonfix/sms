<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class AnalyticsUsers extends Model
{
    protected $table = 'analytics_users';
    protected $fillable = [
        'device_id',
        'activity',
        'page',
        'country',
        'city',
        'village'
    ];
}

<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class AnalyticsShops extends Model
{
    protected $table = 'analytics_shops';
    protected $fillable = [
        'shop_id',
        'device_id',
        'activity',
        'page',
        'country',
        'city',
        'village'
    ];
}

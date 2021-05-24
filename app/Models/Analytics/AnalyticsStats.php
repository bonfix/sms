<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class AnalyticsStats extends Model
{
    protected $table = 'analytics_stats';
    protected $fillable = [
        'analytics_type',
        'analytics_type_id',
        'first_seen',
        'last_seen'
    ];
}

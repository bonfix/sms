<?php


namespace App\Models\Analytics;
use Illuminate\Database\Eloquent\Model;

class AnalyticsDeliveries
{
    protected $table = 'analytics_deliveries';
    protected $fillable = [
        'time',
        'device_id',
        'location',
        'town',
        'status',
        'details'
    ];
}

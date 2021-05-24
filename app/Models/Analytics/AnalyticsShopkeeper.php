<?php

namespace App\Models\Analytics;

use App\User;
use Illuminate\Database\Eloquent\Model;

class AnalyticsShopkeeper extends Model
{
	protected $table = 'analytics_shopkeepers';
	protected $fillable = [
		'user_id',
        'page',
		'activity',
        'country',
        'address_level_1',
        'address_level_2',
        'address_level_3',
        'latitude',
        'longitude'
	];

    public function user() {
        return $this->BelongsTo(User::class);
	}
}
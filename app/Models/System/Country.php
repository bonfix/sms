<?php

namespace App\Models\System;

use App\Models\Items\Item;
use App\Models\Shops\Shop;
use Illuminate\Database\Eloquent\Model;
use App\Models\Regions\Region;
use App\Models\Villages\Village;

class Country extends Model
{

    public $table = 'countries_new';
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'system';
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'name',
        'iso2',
        'code',
        'currency',
        'default_language',
        'languages',
        'phones',
        'show_phone_number',
        'phone_code',
        'fallback_latitude',
        'fallback_longitude',
        'fallback_range',
        'auto_approve_promotions',
        'analytics_shopkeepers_on',
        'analytics_shops_on',
        'analytics_users_on'
    ];

    protected $casts = [
		"show_phone_number" => "boolean"
    ];

    public function items()
    {
    	return $this->hasMany(Item::class, 'country_id');
    }

    public function shops()
    {
    	return $this->hasMany(Shop::class, 'country_id');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Country (:subject.name) has been {$eventName}---:causer.name";
    }

    public function regions() {
        return $this->hasMany(Region::class);
    }

    public function villages() {
        return $this->hasManyThrough(Village::class, Region::class, 'country_id', 'region_id');
    }
}

<?php

namespace App\Models\Wholesalers;

use App\Models\Items\Item;
use App\Models\Items\Promotion;
use App\Models\System\Country;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Wholesaler extends Model implements TranslatableContract
{
    use LogsActivity, Translatable;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defaultLocale = 'en';
    }

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'wfp_shop_id', 'name', 'address_level_1', 'address_level_2', 'address_level_3', 'address_description', 'image_thumb', 'image_display',
        'latitude', 'longitude', 'country_id', 'phone', 'provides_transportation', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'provides_transportation' => 'boolean'
    ];

    public $translatedAttributes = [];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'system';

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_wholesaler')->withPivot('price', 'updated_at');//->orderBy('priority', 'asc');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}

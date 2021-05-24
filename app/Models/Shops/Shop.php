<?php

namespace App\Models\Shops;

use App\Models\Items\Item;
use App\Models\Items\Promotion;
use App\Models\System\Country;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Fixme\Ordering\Contracts\Client\Seller;
use Fixme\Ordering\Traits\ActAsSeller;

class Shop extends Model implements TranslatableContract, Seller
{
    use LogsActivity, Translatable, ActAsSeller;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->defaultLocale = 'ar';
    }

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = [
        'wfp_shop_id',  'image_thumb',
        'image_display', 'latitude', 'longitude', 'country_id', 'phone_number', 'whatsapp_number', 'whatsapp_enabled', 'provides_transportation',
        'is_franchise', 'is_active', 'minimum_order_fee', 'delivery_charge'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'provides_transportation' => 'boolean',
        'whatsapp_enabled' => 'boolean',
        'is_franchise' => 'boolean'
    ];

    public $translatedAttributes = ['name', 'address_level_1', 'address_level_2', 'address_level_3', 'address_description'];

    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $logName = 'system';

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Shop (:subject.name) (:subject.wfp_shop_id) has been {$eventName}---:causer.name";
    }

    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_shop')->withPivot('price', 'updated_at', 'updated_by_shop')->orderBy('order', 'asc');
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'shop_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'shop_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function feedbacks()
    {
        return $this->hasMany(ShopFeedback::class);
    }
}

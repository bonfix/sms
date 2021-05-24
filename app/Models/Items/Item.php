<?php

namespace App\Models\Items;

use App\Models\Shops\Shop;
use App\Models\System\Country;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Fixme\Ordering\Contracts\Client\Item as ClientItem;
use Fixme\Ordering\Traits\ActAsItem;

class Item extends Model implements TranslatableContract, ClientItem
{
    use Translatable, ActAsItem;

    public $translatedAttributes = ['name'];
    public $translationForeignKey = 'item_id';

    protected $dateFormat = 'Y-m-d H:i:s';
    
    protected $fillable = [
        'wfp_product_id',
        'category_id',
        'barcode',
        'image',
        'order',
        'country_id',
        'active'
    ];

    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'item_shop')->withPivot('price');
    }

    public function country() 
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function category() 
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}

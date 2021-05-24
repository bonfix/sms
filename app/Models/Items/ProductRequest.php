<?php

namespace App\Models\Items;

use App\Models\Shops\Shop;
use App\Models\System\Country;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Fixme\Ordering\Contracts\Client\Item as ClientItem;
use Fixme\Ordering\Traits\ActAsItem;

class ProductRequest extends Model
{
    public $table = 'products_requests';
    protected $fillable = [
        'shop_id',
        'user_id',
        'name',
        'barcode',
        'price',
        'image',
        'status'
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

}

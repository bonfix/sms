<?php

namespace App\Models\Shops;

use Illuminate\Database\Eloquent\Model;

class ShopTranslation extends Model
{
    public $table = 'shops_translations';
    public $timestamps = false;
    protected $fillable = ['name', 'address_level_1', 'address_level_2', 'address_level_3', 'address_description'];
}

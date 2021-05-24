<?php

namespace App\Models\Items;

use App\Models\Shops\Shop;
use App\Models\Items\Promotion;
use Illuminate\Database\Eloquent\Model;

class ItemShop extends Model
{
    protected $table = 'item_shop';
    protected $fillable = ['item_id', 'shop_id', 'price', 'updated_by_shop'];    
}

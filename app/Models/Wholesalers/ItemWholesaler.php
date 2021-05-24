<?php

namespace App\Models\Wholesalers;

use App\Models\Items\Item;
use App\Models\Wholesalers\Wholesaler;
use Illuminate\Database\Eloquent\Model;

class ItemWholesaler extends Model
{
	protected $table = 'item_wholesaler';
	protected $fillable = [
		'item_id',
		'wholesaler_id',
		'price'
	];

    public function shop() {
        return $this->BelongsTo(Wholesaler::class);
	}

    public function item() {
        return $this->BelongsTo(Item::class);
    }
}
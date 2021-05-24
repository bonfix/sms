<?php

namespace App\Models\Analytics;

use App\Models\Items\Item;
use App\User;
use Illuminate\Database\Eloquent\Model;

class AnalyticsItems extends Model
{
	protected $table = 'analytics_items';
	protected $fillable = [
		'user_id',
        'item_id',
		'activity',
        'price_before',
        'price_current'
	];

    public function user() {
        return $this->BelongsTo(User::class);
	}
    public function item() {
        return $this->BelongsTo(Item::class);
    }
}
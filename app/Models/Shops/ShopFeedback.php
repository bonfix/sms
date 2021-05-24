<?php

namespace App\Models\Shops;

use Illuminate\Database\Eloquent\Model;

class ShopFeedback extends Model
{
	protected $table = 'shops_feedback';
	protected $fillable = [
		'device_id',
		'mobile_number',
		'input',
		'shop_feedback_option_id',
		'shop_id',
		'is_negative',
	];

    public function shop() {
        return $this->BelongsTo(Shop::class);
	}
	
	public function FeedbackOptions(){
        
        return $this->belongsToMany(ShopFeedbackOption::class, 'shops_feedback_relat', 'feedback_id', 'option_id');
	}
	
	public function FeedbackOption(){
        
        return $this->hasOne(ShopFeedbackOption::class, 'id', 'shop_feedback_option_id');
    }
}
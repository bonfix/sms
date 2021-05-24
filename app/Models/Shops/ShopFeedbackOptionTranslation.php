<?php

namespace App\Models\Shops;

use Illuminate\Database\Eloquent\Model;

class ShopFeedbackOptionTranslation extends Model
{
	public $table = 'shop_feedback_option_translations';
    public $timestamps = false;
    protected $fillable = ['description'];
}
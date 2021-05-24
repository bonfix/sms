<?php

namespace App\Models\Shops;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class ShopFeedbackOption extends Model implements TranslatableContract
{
    use Translatable;

    public $table = 'shop_feedback_options';
    public $translatedAttributes = ['description'];
    protected $fillable = ['is_negative'];
    public $translationForeignKey = 'option_id';
}

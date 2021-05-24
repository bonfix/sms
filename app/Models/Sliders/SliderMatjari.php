<?php

namespace App\Models\Sliders;

use App\Models\System\Country;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class SliderMatjari extends Model implements TranslatableContract
{
    use Translatable;

    public $table = 'sliders_matjari';
    public $translatedAttributes = ['title', 'description'];
    protected $fillable = ['country_id', 'photo', 'active', 'order'];
    public $translationForeignKey = 'slider_id';

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}

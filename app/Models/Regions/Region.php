<?php

namespace App\Models\Regions;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use App\Models\System\Country;
use App\Models\Villages\Village;

class Region extends Model implements TranslatableContract
{
    use Translatable;

    public $table = 'regions';
    public $translatedAttributes = ['name'];
    protected $fillable = ['country_id'];
    public $translationForeignKey = 'region_id';

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function villages()
    {
        return $this->hasMany(Village::class);
    }
}

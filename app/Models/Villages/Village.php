<?php

namespace App\Models\Villages;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use App\Models\Regions\Region;

class Village extends Model implements TranslatableContract
{
    use Translatable;

    public $table = 'villages';
    public $translatedAttributes = ['name'];
    protected $fillable = ['region_id', 'longitude', 'latitude'];
    public $translationForeignKey = 'village_id';

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}

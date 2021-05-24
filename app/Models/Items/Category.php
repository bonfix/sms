<?php

namespace App\Models\Items;

use App\Models\System\Country;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Category extends Model implements TranslatableContract
{
    use Translatable;

    public $table = 'categories';
    public $translatedAttributes = ['name'];
    protected $fillable = ['country_id', 'parent_id', 'photo', 'active', 'order'];
    public $translationForeignKey = 'category_id';

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function child()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}

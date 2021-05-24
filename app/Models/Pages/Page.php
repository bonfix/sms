<?php

namespace App\Models\Pages;

use App\Models\System\Country;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Page extends Model implements TranslatableContract
{
    use Translatable;

    public $table = 'pages';
    public $translatedAttributes = ['title', 'description'];
    protected $fillable = ['country_id', 'ident', 'active'];
    public $translationForeignKey = 'page_id';

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}

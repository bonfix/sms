<?php

namespace App\Models\Notifications;

use App\Models\System\Country;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Topic extends Model implements TranslatableContract
{
    use Translatable;

    public $table = 'notification_topics';    
    public $translatedAttributes = ['name'];
    protected $fillable = ['country_id', 'ident', 'active', 'order'];
    public $translationForeignKey = 'topic_id';

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}

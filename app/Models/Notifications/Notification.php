<?php

namespace App\Models\Notifications;

use App\Models\System\Country;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Notification extends Model implements TranslatableContract
{
    use Translatable;

    public $table = 'notifications';    
    public $translatedAttributes = ['title', 'description'];
    protected $fillable = ['country_id', 'app', 'type', 'type_ids', 'data', 'success_number', 'faild_number'];
    public $translationForeignKey = 'notification_id';

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}

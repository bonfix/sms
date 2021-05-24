<?php

namespace Bonfix\DaliliSms\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class SmsMenuOption extends Model
{
    use Translatable;

    const TABLE_NAME = 'sms_menu_options';
    protected $fillable = [
        "name", "value", "order", "menu_id"
    ];

    public $translatedAttributes = ['description'];
    public $translationForeignKey = 'menu_option_id';

    public function menu(){
        return $this->belongsTo(SmsMenu::class, 'menu_id', 'id');
    }
}

<?php

namespace Bonfix\DaliliSms\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class SmsMenu extends Model
{
    use Translatable;

    const TABLE_NAME = 'sms_menus';
    protected $fillable = [
        "name", "order", "allowMultiple"
    ];

    public $translatedAttributes = ['description'];
    public $translationForeignKey = 'menu_id';

    public function options(){
        return $this->hasMany(SmsMenuOption::class, 'menu_id', 'id');
    }
}

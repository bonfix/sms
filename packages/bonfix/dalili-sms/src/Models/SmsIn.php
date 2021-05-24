<?php

namespace Bonfix\DaliliSms\Models;

use Illuminate\Database\Eloquent\Model;

class SmsIn extends Model
{
    const TABLE_NAME = 'sms_in';

    protected $fillable = [
        "user_id","message","option", "menu_id", "isInvalid"
    ];
    public function __construct(array $attributes = array())
    {
        $this->setTable(config('sms.table_prefix', 'sms_').'in');
        parent::__construct($attributes);
    }
}

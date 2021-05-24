<?php

namespace Bonfix\DaliliSms\Models;

use Illuminate\Database\Eloquent\Model;

class SmsUser extends Model
{
    const TABLE_NAME = 'sms_users';
    protected $fillable = [
        "phone","username","language_code","region_id","village_id",
        "last_request_time","country_code", "isActive"
    ];
    public function __construct(array $attributes = array())
    {
        $this->setTable(config('sms.table_prefix', 'sms_').'users');
        parent::__construct($attributes);
    }
}

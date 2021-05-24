<?php

namespace Bonfix\DaliliSms\Models;

use Illuminate\Database\Eloquent\Model;

class SmsOut extends Model
{
    const TABLE_NAME = 'sms_out';

    protected $fillable = [
        "user_id","message", "menu_id", "page", "next_menu", "cache", "is_delivered", "is_sent", "prev_item"
    ];
    public function __construct(array $attributes = array())
    {
        $this->setTable(config('sms.table_prefix', 'sms_').'out');
        parent::__construct($attributes);
    }
}

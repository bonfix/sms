<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Model;

class TopicTranslation extends Model
{
    public $table = 'notification_topic_translations';
    public $timestamps = false;
    protected $fillable = ['name'];
}

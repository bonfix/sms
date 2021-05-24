<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Model;

class NotificationTranslation extends Model
{
    public $table = 'notification_translations';
    public $timestamps = false;
    protected $fillable = ['title', 'description'];
}

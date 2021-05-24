<?php

namespace App\Models\Notifications;

use Illuminate\Database\Eloquent\Model;
use Fixme\Ordering\Contracts\Client\Buyer;
use Fixme\Ordering\Traits\ActAsBuyer;

class NotificationUser extends Model implements Buyer
{    
    use ActAsBuyer;
    
    public $table = 'notifications_users';
}
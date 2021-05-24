<?php

namespace App\Models\Auth;

use Fixme\Ordering\Contracts\Client\Staff;
use Fixme\Ordering\Traits\ActAsStaff;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class Admin extends Authenticatable implements Staff
{
    use Notifiable;
    use LogsActivity;
    use ActAsStaff;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected static $logAttributes = ['*'];    
    protected static $logOnlyDirty = true;
    protected static $logName = 'system';
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Admin (:subject.name) has been {$eventName}---:causer.name";
    }
}

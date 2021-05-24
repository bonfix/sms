<?php

namespace App\Models\Validation;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\System\Country;

class ValidationLocation extends Model
{
	use LogsActivity;
	
    protected $dateFormat = 'Y-m-d H:i:s';

	protected $fillable = [
		'country_id',
		'district',
		'village',
		'address',
		'office_name',
		'latitude',
		'longitude',
		'type_id',
		'stations_count'
	];

	protected static $logAttributes = ['*'];    
    protected static $logOnlyDirty = true;
    protected static $logName = 'system';

    public function officeType()
    {
        return $this->belongsTo(OfficeType::class, 'type_id');
	}
	
    public function getDescriptionForEvent(string $eventName): string
    {
        return "Office (:subject.office_name) has been {$eventName}---:causer.name";
    }

	public function country() {
		return $this->belongsTo(Country::class, 'country_id');
	}
	
	public function feedback(){
		return $this->hasMany(StationFeedback::class, 'station_id');
	}
}

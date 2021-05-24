<?php

namespace App\Models\Analytics;

use App\Models\Villages\Village;
use Illuminate\Database\Eloquent\Model;

class AnalyticsSession extends Model
{
    protected $fillable = [
        "app","duration","version_code","version_number","device_id","country_id","language","latitude","longitude","username","village_id"
    ];
    public function __construct(array $attributes = array())
    {
        $this->setTable(config('analytics.table_prefix', '').'sessions');
        parent::__construct($attributes);
    }

    public function village()
    {
        return $this->belongsTo(Village::class);
    }
}

<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSessionSearch extends Model
{
    protected $fillable = ["keyword","type","session_id","at"];

    public function __construct(array $attributes = array())
    {
        $this->setTable(config('analytics.table_prefix', '').'session_'.'searches');
        parent::__construct($attributes);
    }
}

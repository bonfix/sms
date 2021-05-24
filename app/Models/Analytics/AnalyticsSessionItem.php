<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSessionItem extends Model
{
    protected $fillable = ["item_id","type","quantity","activity_id","activity"];
    public function __construct(array $attributes = array())
    {
        $this->setTable(config('analytics.table_prefix', '').'session_'.'items');
        parent::__construct($attributes);
    }
}

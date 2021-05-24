<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSessionComparison extends Model
{
    protected $fillable = ["local_basket_id","shop_id","channel","order_id","session_id","at"];

    public function __construct(array $attributes = array())
    {
        $this->setTable(config('analytics.table_prefix', '').'session_'.'comparisons');
        parent::__construct($attributes);
    }
}

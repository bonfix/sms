<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSessionOrder extends Model
{
    protected $fillable = ["local_basket_id","items","shop_id","channel","order_id","session_id","at"];

    public function __construct(array $attributes = array())
    {
        $this->setTable(config('analytics.table_prefix', '').'session_'.'orders');
        parent::__construct($attributes);
    }
}

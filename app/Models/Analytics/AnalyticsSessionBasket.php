<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSessionBasket extends Model
{
    protected $fillable = ["local_basket_id","shop_id","session_id","at"];
    public function __construct(array $attributes = array())
    {
        $this->setTable(config('analytics.table_prefix', '').'session_'.'baskets');
        parent::__construct($attributes);
    }
}

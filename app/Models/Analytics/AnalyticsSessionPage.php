<?php

namespace App\Models\Analytics;

use Illuminate\Database\Eloquent\Model;

class AnalyticsSessionPage extends Model
{
    protected $fillable = ["name","duration","is_modal","next_page", "prev_page","session_id","at"];

    public function __construct(array $attributes = array())
    {
        $this->setTable(config('analytics.table_prefix', '').'session_'.'pages');
        parent::__construct($attributes);
    }
}

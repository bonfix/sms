<?php

namespace App\Models\Items;

use App\Models\Shops\Shop;
use App\Models\Items\Promotion;
use Illuminate\Database\Eloquent\Model;

class PromotionStatus extends Model
{
    protected $table = 'promotion_statuses';
    protected $fillable = ['code', 'description'];

    public function promotion()
    {
        return $this->hasMany(Promotion::class, 'status_id');
    }
}

<?php

namespace App\Models\Items;

use App\Models\Shops\Shop;
use App\Models\Items\PromotionStatus;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Fixme\Ordering\Contracts\Client\Item as ClientItem;
use Fixme\Ordering\Traits\ActAsItem;

class Promotion extends Model implements ClientItem
{
    use LogsActivity, ActAsItem;
    
    protected static $logAttributes = ['*'];    
    protected static $logOnlyDirty = true;
    protected static $logName = 'system';
    protected $dateFormat = 'Y-m-d H:i:s';
    
    protected $fillable = ['shop_id', 'description', 'barcode', 'price', 'image', 'price','status_id','original_price','rejection_reason', 'date_end'
	];

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }


    public function status()
    {
        return $this->belongsTo(PromotionStatus::class, 'status_id');
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Promotion (:subject.description) has been {$eventName}---:causer.name";
    }

}

<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{    
    public $table = 'order_reviews';    
    protected $fillable = ['order_id', 'user_id', 'rate', 'review'];
}
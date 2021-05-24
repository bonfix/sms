<?php

namespace App\Models\Items;

use Illuminate\Database\Eloquent\Model;

class ItemTranslation extends Model
{
    public $table = 'item_translations';
    public $timestamps = false;
    protected $fillable = ['name'];
}

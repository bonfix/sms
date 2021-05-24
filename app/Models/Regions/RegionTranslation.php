<?php

namespace App\Models\Regions;

use Illuminate\Database\Eloquent\Model;

class RegionTranslation extends Model
{
	public $table = 'regions_translations';
    public $timestamps = false;
    protected $fillable = ['name'];
}
<?php

namespace App\Models\Villages;

use Illuminate\Database\Eloquent\Model;

class VillageTranslation extends Model
{
	public $table = 'villages_translations';
    public $timestamps = false;
    protected $fillable = ['name'];
}
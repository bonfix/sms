<?php

namespace App\Models\Sliders;

use Illuminate\Database\Eloquent\Model;

class SliderMatjariTranslation extends Model
{
    public $table = 'sliders_matjari_translations';
    public $timestamps = false;
    protected $fillable = ['title', 'description'];
}

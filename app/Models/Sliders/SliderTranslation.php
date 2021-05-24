<?php

namespace App\Models\Sliders;

use Illuminate\Database\Eloquent\Model;

class SliderTranslation extends Model
{
    public $table = 'slider_translations';
    public $timestamps = false;
    protected $fillable = ['title', 'description'];
}

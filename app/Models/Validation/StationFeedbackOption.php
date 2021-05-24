<?php

namespace App\Models\Validation;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class StationFeedbackOption extends Model implements TranslatableContract
{
    use Translatable;

    public $table = 'stations_feedback_options';
    public $translatedAttributes = ['name'];
    protected $fillable = ['is_negative'];
    public $translationForeignKey = 'option_id';
}

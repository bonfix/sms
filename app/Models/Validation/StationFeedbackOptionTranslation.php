<?php

namespace App\Models\Validation;

use Illuminate\Database\Eloquent\Model;

class StationFeedbackOptionTranslation extends Model
{
    public $table = 'stations_feedback_options_translations';
    public $timestamps = false;
    protected $fillable = ['name'];
}

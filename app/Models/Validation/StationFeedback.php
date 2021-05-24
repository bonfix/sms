<?php

namespace App\Models\Validation;

use Illuminate\Database\Eloquent\Model;

class StationFeedback extends Model
{   
    public $table = 'stations_feedback';

    public function FeedbackOptions(){
        
        return $this->belongsToMany(StationFeedbackOption::class, 'stations_feedback_relat', 'feedback_id', 'option_id');
    }
}

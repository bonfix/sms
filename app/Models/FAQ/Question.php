<?php

namespace App\Models\FAQ;

use App\Models\System\Country;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Question extends Model implements TranslatableContract
{
  use Translatable;

  public $table = 'faq_questions';
  public $translatedAttributes = ['question', 'answer'];
  protected $fillable = ['country_id', 'topic_id', 'active', 'order'];
  public $translationForeignKey = 'question_id';

  public function topic() {
    return $this->belongsTo(Topic::class);
  }

  public function country(){
    return $this->belongsTo(Country::class);
  }
}

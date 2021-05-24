<?php

namespace App\Models\FAQ;

use App\Models\FAQ\Question;
use App\Models\System\Country;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Topic extends Model implements TranslatableContract
{
  use Translatable;

  public $table = 'faq_topics';
  public $translatedAttributes = ['name', 'description'];
  protected $fillable = ['country_id', 'active', 'order'];
  public $translationForeignKey = 'topic_id';

  public function questions() {
    return $this->hasMany(Question::class, 'topic_id');
  }

  public function country(){
    return $this->belongsTo(Country::class);
  }
}

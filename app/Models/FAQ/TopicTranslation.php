<?php

namespace App\Models\FAQ;

use Illuminate\Database\Eloquent\Model;

class TopicTranslation extends Model
{
	protected $table = 'faq_topic_translations';
  public $timestamps = false;
  protected $fillable = ['name', 'description'];
}

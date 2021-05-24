<?php

namespace App\Models\FAQ;

use Illuminate\Database\Eloquent\Model;

class QuestionTranslation extends Model
{
    protected $table = 'faq_question_translations';
    public $timestamps = false;
    protected $fillable = ['question', 'answer'];
}

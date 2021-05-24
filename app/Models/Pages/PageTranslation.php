<?php

namespace App\Models\Pages;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    public $table = 'page_translations';
    public $timestamps = false;
    protected $fillable = ['title', 'description'];
}

<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class CountryFeature extends Model
{    
    protected $table = 'countries_features';
    protected $fillable = ['country_id', 'parent_id', 'feature_title', 'feature_ident', 'feature_description', 'feature_status'];
    
    public function childs()
    {
        return $this->hasMany(CountryFeature::class, 'parent_id', 'id');
    }
}

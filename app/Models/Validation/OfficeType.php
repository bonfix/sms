<?php

namespace App\Models\Validation;

use Illuminate\Database\Eloquent\Model;

class OfficeType extends Model
{
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $fillable = ['name'];
}

<?php

namespace App\Models\System;

use Fixme\Ordering\Contracts\Client\Buyer;
use Fixme\Ordering\Traits\ActAsBuyer;
use Illuminate\Database\Eloquent\Model;


class Beneficiary extends Model implements Buyer
{
    use ActAsBuyer;

    public $table = 'beneficiary_users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'scope_id', 'device_id', 'username', 'status'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }
}

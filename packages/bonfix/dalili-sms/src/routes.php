<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api'], function () {
    //Route::resource('/sms', 'Bonfix\DaliliSms\DaliliSmsController');
    Route::post('/sms', 'Bonfix\DaliliSms\DaliliSmsController@smsIn');
    //Route::get('/sms', 'Bonfix\DaliliSms\DaliliSmsController@smsIn');
});
Route::get('/sms', 'Bonfix\DaliliSms\DaliliSmsController@smsIn');

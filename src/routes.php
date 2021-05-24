<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api/v2'], function () {
    //Route::resource('/sms', 'Bonfix\DaliliSms\DaliliSmsController');
    Route::post('/sms', 'Bonfix\DaliliSms\DaliliSmsController@smsIn');
//    Route::get('/sms', 'Bonfix\DaliliSms\DaliliSmsController@smsIn');
});
//Route::get('/sms', 'Bonfix\DaliliSms\DaliliSmsController@testMethod');
    Route::get('/sms', 'Bonfix\DaliliSms\DaliliSmsController@smsIn');

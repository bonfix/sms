<?php


namespace Bonfix\DaliliSms;


class SmsHelper
{
    //MAIN MENU
    const MENU_GROUP_MAIN = 'MAIN_MENU';
    const MENU_MAIN_REGISTRATION = 'REGISTRATION';
    const MENU_MAIN_LANGUAGE = 'LANGUAGE';
    const MENU_MAIN_USERNAME = 'USERNAME';
    const MENU_MAIN_AREA = 'AREA';
    const MENU_MAIN_SUB_AREA = 'SUB_AREA';
    const MENU_MAIN_OPTIONS = 'MAIN_OPTIONS_MENU';
    const MENU_MAIN_UNSUBSCRIBE = 'UNSUBSCRIBE';

    //COMMON_OPTIONS
    const MENU_GROUP_COMMON_OPTIONS = 'COMMON_OPTIONS_MENU';
    const MENU_COMMON_OPTIONS_MAIN_MENU_CODE = '000';
    const MENU_COMMON_OPTIONS_MORE_MENU_CODE = '00';
    const MENU_COMMON_OPTIONS_BACK_MENU_CODE = '0';

    const MENU_GROUP_PROFILE = 'PROFILE_MENU';
    const MENU_PROFILE_MAIN = 'PROFILE_MAIN';
//    const MENU_PROFILE_LANGUAGE = 'PROFILE_LANGUAGE';
//    const MENU_PROFILE_LOCATION = 'PROFILE_LOCATION';

    const MENU_GROUP_OFFERS = 'OFFERS_MENU';
    const MENU_OFFERS_MAIN = 'OFFERS_MAIN';
    const MENU_OFFERS_RES = 'OFFERS_RES';

    const MENU_GROUP_SHOPS = 'SHOPS_MENU';
    const MENU_SHOPS_MAIN = 'SHOPS_MAIN';
    const MENU_SHOPS_ITEMS = 'SHOPS_ITEMS';
    const MENU_SHOPS_RES = 'SHOPS_RES';

    const MENU_GROUP_PRODUCTS = 'PRODUCTS_MENU';
    const MENU_PRODUCTS_MAIN = 'PRODUCTS_MAIN';
    const MENU_PRODUCTS_SHOPS = 'PRODUCTS_SHOPS';
    const MENU_PRODUCTS_RES = 'PRODUCTS_RES';

    const INVALID_OPTION_MSG = [
        'en' => 'Invalid selection! Try again.',
        'sw' => 'Uteuzi sio sahihi! Jaribu tena.'
    ];
    const ERROR_MSG = [
        'en' => 'Sorry, there was an error with that request.',
        'sw' => 'Samahani, kulikuwa na hitilafu na ombi hilo.'
    ];

    const KEY_DALILI_UNSUBSCRIBE = "DALILI STOP";
    const DALILI_SHORTCODE = "15667";

}
/*
 * php artisan migrate:refresh
 *  php artisan migrate:rollback
 * php artisan migrate --path=packages/bonfix/dalili-sms/src/migrations
 * php artisan db:seed --class="Bonfix\DaliliSms\seeds\DatabaseSeeder"
 *
 * */

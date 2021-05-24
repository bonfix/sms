<?php

namespace Bonfix\DaliliSms;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class DaliliSmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->publishes([
            dirname(__DIR__).'/config.php' => config_path('sms.php'),
            //realpath(__DIR__.'/migrations') => $this->app->databasePath().'/migrations',
        ]);
        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Bonfix\DaliliSms\DaliliSmsController');
    }
}

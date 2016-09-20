<?php

namespace App\Providers;

use Bouncer;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Barryvdh\Debugbar\Middleware\Debugbar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Bouncer::cache();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (env('APP_ENV') && env('APP_DEBUG')) {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
            AliasLoader::getInstance()->alias('Debugbar', \Barryvdh\Debugbar\Facade::class);
        }
    }
}

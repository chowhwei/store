<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\ConfigLoader as ConfigLoaderContract;
use Chowhwei\Store\Store\ConfigLoader;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

class StoreServiceProvider extends ServiceProvider
{
    public function register()
    {
        /**
         * 默认绑定laravel的ConfigLoader
         */
        $this->app->singleton(ConfigLoaderContract::class, function (Container $app) {
            return new ConfigLoader();
        });

        $this->app->singleton(\Chowhwei\Store\Contracts\Factory::class, function(Container $app){
            return new Factory($app->make(ConfigLoaderContract::class));
        });
    }
}
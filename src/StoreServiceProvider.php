<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\StoreFactory as StoreFactoryContract;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

class StoreServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(StoreFactoryContract::class, function (Container $app) {
            return new StoreFactory();
        });
    }
}
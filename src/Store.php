<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\ConfigLoader as ConfigLoaderContract;
use Chowhwei\Store\Store\KohanaConfigLoader;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

class Store extends Container
{
    /** @var Container $app */
    static $app = null;

    protected $providers = [
        StoreServiceProvider::class
    ];

    public function __construct()
    {
        foreach ($this->providers as $provider) {
            /** @var ServiceProvider $provider */
            $provider = new $provider($this);
            $provider->register();
        }

        /**
         * 这里只在laravel外用，使用KohanaConfigLoader覆盖默认
         */
        $this->singleton(ConfigLoaderContract::class, function (Container $app) {
            return new KohanaConfigLoader();
        });
    }

    /**
     * @param string $name
     * @return Contracts\ContentStore
     * @throws \OSS\Core\OssException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    static public function app(string $name){
        if(is_null(self::$app)){
            self::$app = new static();
        }
        /** @var Factory $factory */
        $factory = self::$app->make(\Chowhwei\Store\Contracts\Factory::class);
        return $factory->app($name);
    }
}
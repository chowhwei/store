<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\StoreFactory;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

/**
 * 在kohana下使用的
 * Class Store
 * @package Chowhwei\Store
 */
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
        /** @var StoreFactory $factory */
        $factory = self::$app->make(StoreFactory::class);
        return $factory->app($name);
    }
}
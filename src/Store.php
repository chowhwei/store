<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\ConfigLoader as ConfigLoaderContract;
use Chowhwei\Store\Contracts\ContentStore;
use Chowhwei\Store\Store\KohanaConfigLoader;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

class Store extends Container
{
    /**
     * @param string $app
     * @return ContentStore
     */
    static public function contentStore(string $app = 'default')
    {
        static $instances = [];
        if (!isset($instances[$app])) {
            $store = new self($app);
            $instances[$app] = $store->make(ContentStore::class);
        }
        return $instances[$app];
    }

    protected $providers = [
        StoreServiceProvider::class
    ];

    public function __construct(string $app)
    {
        $this->instance('store_app', $app);

        foreach ($this->providers as $provider) {
            /** @var ServiceProvider $provider */
            $provider = new $provider($this);
            $provider->register();
        }

        /**
         * 这里只在laravel外用，使用KohanaConfigLoader覆盖默认
         */
        $this->singleton(ConfigLoaderContract::class, function (Store $store) {
            return new KohanaConfigLoader();
        });
    }
}
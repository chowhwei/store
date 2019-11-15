<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\ConfigLoader as ConfigLoaderContract;
use Chowhwei\Store\Contracts\ContentStore as ContentStoreContract;
use Chowhwei\Store\Contracts\StoreConfig as StoreConfigContract;
use Chowhwei\Store\Store\ConfigLoader;
use Chowhwei\Store\Store\FileClient;
use Chowhwei\Store\Store\OssClient;
use Chowhwei\Store\Store\StoreConfig;
use Illuminate\Support\ServiceProvider;
use Chowhwei\Store\Contracts\KeyStore as KeyStoreContract;

class StoreServiceProvider extends ServiceProvider
{
    public function register()
    {
        /**
         * 默认绑定laravel的ConfigLoader
         */
        $this->app->singleton(ConfigLoaderContract::class, function (Store $store) {
            return new ConfigLoader();
        });

        $this->app->singleton(StoreConfigContract::class, function (Store $store) {
            return new StoreConfig($store->make(ConfigLoaderContract::class), $store->make('store_app'));
        });

        $this->app->singleton(KeyStoreContract::class, function (Store $store) {
            return new KeyStore($store->make(OssClient::class), $store->make(FileClient::class));
        });

        $this->app->singleton(ContentStoreContract::class, function (Store $store) {
            return new ContentStore($store->make(OssClient::class), $store->make(FileClient::class));
        });
    }
}
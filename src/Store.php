<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\ContentStore as ContentStoreContract;
use Chowhwei\Store\Store\FileClient;
use Chowhwei\Store\Store\KohanaConfigLoader;
use Chowhwei\Store\Store\OssClient;
use Chowhwei\Store\Store\StoreConfig;
use Exception;

/**
 * 在laravel框架之外，使用Store充当容器
 *
 * $storeApp = new StoreApp();
 * $cs = $storeApp->make(ContentStore::class);
 * $cs = Store::contentStore('toc');
 *
 * Class Store
 * @package Chowhwei\Store
 */
class Store
{
    static protected $store_apps = [];

    /**
     * @param string $store_app
     * @return ContentStoreContract
     * @throws Exception
     */
    static public function app(string $store_app = 'default')
    {
        $configLoader = new KohanaConfigLoader();
        $config = new StoreConfig($configLoader, $store_app);
        $store_app = $config->getStoreApp();
        if (!isset(self::$store_apps[$store_app])) {
            $ossClient = new OssClient($config);
            $fileClient = new FileClient($config);
            self::$store_apps[$store_app] = new ContentStore($ossClient, $fileClient);
        }
        return self::$store_apps[$store_app];
    }
}
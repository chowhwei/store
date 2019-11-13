<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\StoreConfig as StoreConfigContract;

class StoreConfig implements StoreConfigContract
{
    protected $app;
    protected $config;

    public function __construct($framework, $store_app = null)
    {
        if (is_null($store_app)) {
            $store_app = $this->getConfig($framework, 'store.default');
        }
        $app = $store_app;
        $this->config = $this->getConfig($framework, 'store.apps.' . $app);
    }

    protected function getConfig($framework, $key)
    {
        $sf = strtolower($framework);
        $config = null;
        switch ($sf) {
            case 'laravel':
                $config = config($key);
                break;
            case 'kohana':
                $config = Kohana::$config->load($key);
                break;
        }
        return $config;
    }

    public function getOssEndPoint(): string
    {
        return $this->config['oss_endpoint'];
    }

    public function getOssKeyId(): string
    {
        return $this->config['oss_keyid'];
    }

    public function getOssKeySecret(): string
    {
        return $this->config['oss_keysecret'];
    }

    public function getOssBucket(): string
    {
        return $this->config['oss_bucket'];
    }

    public function getNfsRoot(): string
    {
        return $this->config['nfs_root'];
    }

    public function getStoreApp(): string
    {
        return $this->app;
    }

}
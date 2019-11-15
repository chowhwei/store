<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\ConfigLoader;
use Chowhwei\Store\Contracts\StoreConfig as StoreConfigContract;
use Exception;
use Illuminate\Config\Repository as Config;

class StoreConfig implements StoreConfigContract
{
    protected $app;
    protected $config;

    /**
     * StoreConfig constructor.
     * @param ConfigLoader $configLoader
     * @param string $app
     * @throws Exception
     */
    public function __construct(ConfigLoader $configLoader, string $app)
    {
        $full_config = $configLoader->load('store');

        if ($app == 'default') {
            $app = $full_config->get('default');
        }

        $this->app = $app;
        $this->config = $this->parseConfig($full_config, $app);
    }

    /**
     * @param Config $config
     * @param string $app
     * @return Config
     * @throws Exception
     */
    protected function parseConfig(Config $config, string $app)
    {

        $oss = $config->get('store.' . $app . '.oss');
        $file = $config->get('store.' . $app . '.file');
        if(is_null($oss) || is_null($file)){
            throw new Exception('配置错误');
        }

        $config = $config->get('oss.' . $oss)
            + $config->get('file.' . $file);

        return new Config($config);
    }

    public function getOssEndPoint(): string
    {
        return $this->config->get('oss_endpoint', '');
    }

    public function getOssKeyId(): string
    {
        return $this->config->get('oss_keyid', '');
    }

    public function getOssKeySecret(): string
    {
        return $this->config->get('oss_keysecret', '');
    }

    public function getOssBucket(): string
    {
        return $this->config->get('oss_bucket', '');
    }

    public function getNfsRoot(): string
    {
        return $this->config->get('nfs_root', '');
    }

    public function getStoreApp(): string
    {
        return $this->app;
    }

}
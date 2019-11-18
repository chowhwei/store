<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\ConfigLoader;
use Chowhwei\Store\Store\BaseStore;
use Chowhwei\Store\Store\FileClient;
use Chowhwei\Store\Store\OssClient;
use Illuminate\Config\Repository;
use Chowhwei\Store\Contracts\Factory as FactoryContract;

class Factory implements FactoryContract
{
    /** @var BaseStore[] $apps */
    protected $apps = [];
    /** @var Repository $config */
    protected $config;
    protected $ossClients = [];
    protected $fileClients = [];

    public function __construct(ConfigLoader $configLoader)
    {
        $this->config = $configLoader->load('store');
    }

    /**
     * @param string $app
     * @return \Chowhwei\Store\Contracts\ContentStore
     * @throws \OSS\Core\OssException
     */
    public function app(string $app = 'default')
    {
        return $this->getApp($app);
    }

    /**
     * @param $app
     * @return \Chowhwei\Store\Contracts\ContentStore
     * @throws \OSS\Core\OssException
     * @throws \Exception
     */
    protected function getApp($app)
    {
        if($app == 'default'){
            $app = $this->config->get('default');
        }

        if(!isset($this->apps[$app])){
            $config = $this->config->get("store.{$app}");

            $ossClient = $this->getOssClient($this->config->get("oss.{$config['oss']}"), $config['oss'], $app);
            $fileClient = $this->getFileClient($this->config->get("file.{$config['file']}"), $config['file'], $app);

            $this->apps[$app] = new ContentStore($ossClient, $fileClient);
        }

        return $this->apps[$app];
    }

    /**
     * @param $config
     * @param $oss
     * @param $app
     * @return OssClient
     * @throws \OSS\Core\OssException
     */
    protected function getOssClient($config, $oss, $app)
    {
        if(!isset($this->ossClients[$oss])){
            $this->ossClients[$oss] = new OssClient($config, $app);
        }

        return $this->ossClients[$oss];
    }

    /**
     * @param $config
     * @param $file
     * @param $app
     * @return FileClient
     * @throws \Exception
     */
    protected function getFileClient($config, $file, $app)
    {
        if(!isset($this->fileClients[$file])){
            $this->fileClients[$file] = new FileClient($config, $app);
        }
        return $this->fileClients[$file];
    }
}
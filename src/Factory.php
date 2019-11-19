<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\ClientFactory;
use Chowhwei\Store\Contracts\ConfigLoader;
use Chowhwei\Store\Contracts\Factory as FactoryContract;
use Chowhwei\Store\Store\BaseStore;
use Chowhwei\Store\Store\FileClient;
use Chowhwei\Store\Store\OssClient;

class Factory implements FactoryContract
{
    /** @var BaseStore[] $apps */
    protected $apps = [];
    /** @var ConfigLoader $configLoader */
    protected $configLoader;
    protected $ossClients = [];
    protected $fileClients = [];

    /** @var ClientFactory $clientFactory */
    protected $clientFactory;

    public function __construct(ConfigLoader $configLoader, ClientFactory $clientFactory)
    {
        $this->clientFactory = $clientFactory;
        $this->configLoader = $configLoader;
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
        $all_config = $this->configLoader->load('store');
        if($app == 'default'){
            $app = $all_config->get('default');
        }

        if(!isset($this->apps[$app])){
            $config = $all_config->get("store.{$app}");

            $ossClient = $this->getOssClient($all_config->get("client.{$config['oss']}"), $config['oss'], $app);
            $fileClient = $this->getFileClient($all_config->get("client.{$config['file']}"), $config['file'], $app);

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
            $this->ossClients[$oss] = $this->clientFactory->makeClient($config, $app);
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
            $this->fileClients[$file] = $this->clientFactory->makeClient($config, $app);
        }
        return $this->fileClients[$file];
    }
}
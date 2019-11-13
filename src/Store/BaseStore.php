<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\StoreClient;
use Chowhwei\Store\Contracts\StoreConfig;
use Exception;

abstract class BaseStore
{
    /** @var StoreConfig $config */
    protected $config;
    /** @var StoreClient $ossClient */
    protected $ossClient;
    /** @var StoreClient $fileClient */
    protected $fileClient;

    /**
     * KeyStore constructor.
     * @param StoreConfig $config
     * @throws Exception
     */
    public function __construct(StoreConfig $config)
    {
        $this->config = $config;
        $this->ossClient = new OssClient($this->config->getOssKeyId(), $this->config->getOssKeySecret(), $this->config->getOssEndPoint(), $this->config->getOssBucket(), $this->config->getStoreApp());
        $this->fileClient = new FileClient($this->config->getNfsRoot(), $this->config->getStoreApp());
    }
}
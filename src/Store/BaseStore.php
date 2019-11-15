<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\StoreClient;
use Exception;

abstract class BaseStore
{
    /** @var StoreClient $ossClient */
    protected $ossClient;
    /** @var StoreClient $fileClient */
    protected $fileClient;

    /**
     * KeyStore constructor.
     * @param OssClient $ossClient
     * @param FileClient $fileClient
     * @throws Exception
     */
    public function __construct(OssClient $ossClient, FileClient $fileClient)
    {
        $this->ossClient = $ossClient;
        $this->fileClient = $fileClient;
    }
}
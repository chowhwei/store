<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\StoreClient;
use Exception;

class BaseStore
{
    /** @var StoreClient $ossClient */
    protected $ossClient;
    /** @var StoreClient $fileClient */
    protected $fileClient;

    /**
     * KeyStore constructor.
     * @param StoreClient $ossClient
     * @param StoreClient $fileClient
     * @throws Exception
     */
    public function __construct(StoreClient $ossClient, StoreClient $fileClient)
    {
        $this->ossClient = $ossClient;
        $this->fileClient = $fileClient;
    }
}
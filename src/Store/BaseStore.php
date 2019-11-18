<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\OssClient;
use Chowhwei\Store\Contracts\FileClient;
use Exception;

abstract class BaseStore
{
    /** @var OssClient $ossClient */
    protected $ossClient;
    /** @var FileClient $fileClient */
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
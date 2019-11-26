<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\ContentStore as ContentStoreContract;
use Chowhwei\Store\Contracts\StoreClient;
use Chowhwei\Store\Store\ContentStoreMeta as MetaContract;
use Exception;

class ContentStore implements ContentStoreContract
{
    /** @var StoreClient $ossClient */
    protected $ossClient;
    /** @var StoreClient $fileClient */
    protected $fileClient;
    /** @var MetaContract $meta */
    protected $meta;

    /**
     * KeyStore constructor.
     * @param StoreClient $ossClient
     * @param StoreClient $fileClient
     * @param MetaContract $meta
     * @throws Exception
     */
    public function __construct(StoreClient $ossClient, StoreClient $fileClient, MetaContract $meta)
    {
        $this->ossClient = $ossClient;
        $this->fileClient = $fileClient;
        $this->meta = $meta;
    }

    public function store(string $key, $content)
    {
        $this->ossClient->set($key, $content);
        $this->fileClient->set($key, $content);
        if(!is_null($this->meta)) {
            $this->meta->saveMeta($key, strlen($content));
        }
    }

    public function get(string $key)
    {
        try {
            $val = $this->fileClient->get($key);
        }catch (Exception $ex)
        {
            $val = null;
        }
        if (is_null($val)) {
            $val = $this->ossClient->get($key);
        }
        return $val;
    }

    public function del(string $key)
    {
        $this->ossClient->del($key);
        $this->fileClient->del($key);
        if(!is_null($this->meta)) {
            $this->meta->decrReference($key);
        }
    }
    /**
     * @param string $content
     * @return string
     * @throws Exception
     */
    public function storeContent(string $content): string
    {
        $hash = hash('sha256', $content);
        if(is_null($this->ossClient->get($hash))){
            $this->store($hash, $content);
        } else {
            if(!is_null($this->meta)) {
                $this->meta->incrReference($hash);
            }
        }
        return $hash;
    }
}
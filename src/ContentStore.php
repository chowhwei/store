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
    protected $meta = null;

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

    public function setMeta($meta)
    {
        $this->meta = $meta;
    }

    public function store(string $key, $content)
    {
        $this->ossClient->set($key, $content);
        $this->fileClient->set($key, $content);
        if (!is_null($this->meta)) {
            $this->meta->saveMeta($key, strlen($content));
        }
    }

    public function get(string $key)
    {
        try {
            $val = $this->fileClient->get($key);
        } catch (Exception $ex) {
            $val = null;
        }
        if (is_null($val)) {
            $val = $this->ossClient->get($key);
        }
        return $val;
    }

    public function del(string $key)
    {
        if (!is_null($this->meta)) {
            $this->meta->decrReference($key);
        }
        $this->ossClient->del($key);
        $this->fileClient->del($key);
    }

    /**
     * @param string $content
     * @return string
     * @throws Exception
     */
    public function storeContent(string $content): string
    {
        $hash = hash('sha256', $content);
        if (is_null($this->ossClient->get($hash))) {
            $this->store($hash, $content);
        } else {
            if (!is_null($this->meta)) {
                $this->meta->incrReference($hash);
            }
        }
        return $hash;
    }

    /**
     * @param string $content
     * @param string $old_key
     * @return string
     * @throws Exception
     */
    public function replaceContent(string $content, string $old_key): string
    {
        $hash = hash('sha256', $content);
        if($hash == $old_key){
            return $hash;
        }

        if (!is_null($this->meta)) {
            $this->meta->decrReference($old_key);
        }
        if (is_null($this->ossClient->get($hash))) {
            $this->store($hash, $content);
        } else {
            if (!is_null($this->meta)) {
                $this->meta->incrReference($hash);
            }
        }
        return $hash;
    }
}
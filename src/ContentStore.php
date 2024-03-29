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
    /** @var StoreClient $nasClient */
    protected $nasClient;
    /** @var MetaContract $meta */
    protected $meta = null;

    /**
     * KeyStore constructor.
     * @param StoreClient $ossClient
     * @param StoreClient $nasClient
     * @throws Exception
     */
    public function __construct(StoreClient $ossClient, StoreClient $nasClient)
    {
        $this->ossClient = $ossClient;
        $this->nasClient = $nasClient;
    }

    public function setMeta($meta)
    {
        $this->meta = $meta;
    }

    public function store(string $key, $content)
    {
        $this->ossClient->set($key, $content);
        $this->nasClient->set($key, $content);
        if (!is_null($this->meta)) {
            $this->meta->saveMeta($key, strlen($content));
        }
    }

    public function get(string $key)
    {
        try {
            $val = $this->nasClient->get($key);
        } catch (Exception $ex) {
            $val = null;
        }
        if (is_null($val)) {
            $val = $this->ossClient->get($key);
            if (!is_null($val)) {
                try {
                    $this->nasClient->set($key, $val);
                }catch (Exception $ex){

                }
            }
        }
        return $val;
    }

    public function del(string $key)
    {
        if (!is_null($this->meta)) {
            $this->meta->decrReference($key);
        }
        $this->ossClient->del($key);
        $this->nasClient->del($key);
    }

    /**
     * @param string $key
     * @return int
     */
    public function has(string $key)
    {
        $has = 0;
        if ($this->ossClient->has($key)) {
            $has |= 1;
        }

        if ($this->nasClient->has($key)) {
            $has |= 1 << 1;
        }
        return $has;
    }

    /**
     * @param string $content
     * @return string
     * @throws Exception
     */
    public function storeContent(string $content): string
    {
        $hash = hash('sha256', $content);
        if (!$this->ossClient->has($hash)) {
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
        $ossHas = $this->ossClient->has($hash);
        if($ossHas) {
            if ($hash == $old_key) {
                return $hash;
            }
        }

        if (!is_null($this->meta)) {
            $this->meta->decrReference($old_key);
        }
        if (!$ossHas) {
            $this->store($hash, $content);
        } else {
            if (!is_null($this->meta)) {
                $this->meta->incrReference($hash);
            }
        }
        return $hash;
    }
}
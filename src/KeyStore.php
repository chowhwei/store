<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\KeyStore as KeyStoreContract;
use Chowhwei\Store\Contracts\StoreConfig;
use Chowhwei\Store\Store\BaseStore;
use Exception;

class KeyStore extends BaseStore implements KeyStoreContract
{
    /**
     * KeyStore constructor.
     * @param StoreConfig $config
     * @throws Exception
     */
    public function __construct(StoreConfig $config)
    {
        parent::__construct($config);
    }

    public function store(string $key, string $content)
    {
        $this->ossClient->set($key, $content);
        $this->fileClient->set($key, $content);
    }

    public function get(string $key): string
    {
        $val = $this->fileClient->get($key);
        if (is_null($val)) {
            $val = $this->ossClient->get($key);
        }
        return $val;
    }

    public function del(string $key)
    {
        $this->ossClient->del($key);
        $this->fileClient->del($key);
    }
}
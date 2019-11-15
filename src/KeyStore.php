<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\KeyStore as KeyStoreContract;
use Chowhwei\Store\Store\BaseStore;

class KeyStore extends BaseStore implements KeyStoreContract
{
    public function store(string $key, $content)
    {
        $this->ossClient->set($key, $content);
        $this->fileClient->set($key, $content);
    }

    public function get(string $key)
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
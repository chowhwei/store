<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\ContentStore as ContentStoreContract;
use Chowhwei\Store\Contracts\StoreConfig;
use Chowhwei\Store\Store\BaseStore;
use Exception;

class ContentStore extends BaseStore implements ContentStoreContract
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

    public function store(string $content): string
    {
        // TODO: Implement store() method.
    }

    public function get(string $hash): string
    {
        // TODO: Implement get() method.
    }

    public function del(string $hash)
    {
        // TODO: Implement del() method.
    }
}
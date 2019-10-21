<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\Store as StoreContract;

class Store implements StoreContract
{
    const HASHING_ALGORITHM = 'sha256';

    public function store(string $bucket, string $content): string
    {
        $key = $this->hash($content);

    }

    public function get(string $bucket, string $content): string
    {
        // TODO: Implement get() method.
    }

    public function mark(string $bucket, string $hash_key)
    {
        // TODO: Implement mark() method.
    }

    public function unmark(string $bucket, string $hash_key)
    {
        // TODO: Implement unmark() method.
    }

    protected function hash(string $content): string
    {
        return hash(self::HASHING_ALGORITHM, $content);
    }
}
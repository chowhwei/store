<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\Store as StoreContract;
use Chowhwei\Store\Contracts\TableStore;
use Chowhwei\Store\Models\Store as StoreStore;

class Store implements StoreContract
{
    const HASHING_ALGORITHM = 'sha256';

    protected $tableStore;
    protected $storeStore;

    public function __construct(TableStore $tableStore, StoreStore $storeStore)
    {
        $this->tableStore = $tableStore;
        $this->storeStore = $storeStore;
    }

    public function store(string $bucket, string $content, string $expires_at = null): string
    {
        $hash_key = $this->hash($content);

        $rec = [
            'bucket' => $bucket,
            'hash' => $hash_key,
            'length' => strlen($content)
        ];

        if (!is_null($expires_at) && ($expires_at > date('Y-m-d H:i:s'))) {
            $rec['expires_at'] = $expires_at;
        }

        return $hash_key;
    }

    public function get(string $bucket, string $hash_key): string
    {
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
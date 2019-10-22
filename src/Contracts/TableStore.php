<?php

namespace Chowhwei\Store\Contracts;

interface TableStore
{
    public function store(string $bucket, string $hash_key, int $length, string $expires_at = null);

    public function get(string $bucket, string $hash_key);

    public function mark(string $bucket, string $hash_key);

    public function unmark(string $bucket, string $hash_key);

    public function getExpires(string $bucket, int $page, int $page_size);
}
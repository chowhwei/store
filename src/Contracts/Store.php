<?php

namespace Chowhwei\Store\Contracts;

interface Store
{
    public function store(string $bucket, string $content, string $expires_at = null): string;

    public function get(string $bucket, string $hash_key): string;

    public function mark(string $bucket, string $hash_key);

    public function unmark(string $bucket, string $hash_key);
}
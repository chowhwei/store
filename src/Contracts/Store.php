<?php

namespace Chowhwei\Store\Contracts;

interface Store
{
    public function store(string $bucket, string $content): string;

    public function get(string $bucket, string $content): string;

    public function mark(string $bucket, string $hash_key);

    public function unmark(string $bucket, string $hash_key);
}
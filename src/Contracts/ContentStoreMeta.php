<?php

namespace Chowhwei\Store\Contracts;

interface ContentStoreMeta
{
    public function saveMeta(string $key, int $size);

    public function incrReference(string $key);

    public function decrReference(string $key);
}
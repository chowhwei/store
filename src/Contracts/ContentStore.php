<?php

namespace Chowhwei\Store\Contracts;

use Exception;

interface ContentStore
{
    /**
     * @param string $content
     * @return string
     * @throws Exception
     */
    public function store(string $content): string;

    /**
     * @param string $hash
     * @return string
     * @throws Exception
     */
    public function get(string $hash): string;

    /**
     * @param string $hash
     * @return void
     * @throws Exception
     */
    public function del(string $hash);
}
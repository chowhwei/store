<?php

namespace Chowhwei\Store\Contracts;

use Exception;

interface KeyStore
{
    /**
     * @param string $key
     * @param string $content
     * @return void
     * @throws Exception
     */
    public function store(string $key, string $content);

    /**
     * @param string $key
     * @return string
     * @throws Exception
     */
    public function get(string $key): string;

    /**
     * @param string $key
     * @return void
     * @throws Exception
     */
    public function del(string $key);
}
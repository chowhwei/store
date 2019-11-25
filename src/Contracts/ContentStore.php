<?php

namespace Chowhwei\Store\Contracts;

use Exception;

interface ContentStore
{
    /**
     * @param string $key
     * @param $content
     * @return void
     * @throws Exception
     */
    public function store(string $key, $content);

    /**
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    public function get(string $key);

    /**
     * @param string $key
     * @return void
     * @throws Exception
     */
    public function del(string $key);

    /**
     * @param string $content
     * @return string
     * @throws Exception
     */
    public function storeContent(string $content): string;
}
<?php

namespace Chowhwei\Store\Contracts;

use Exception;

interface StoreClient
{
    /**
     * @param string $id
     * @param null $default
     * @return string
     * @throws Exception
     */
    public function get(string $id, $default = null): string;

    /**
     * @param string $id
     * @param mixed $data
     * @return bool
     * @throws Exception
     */
    public function set(string $id, $data): bool;

    /**
     * @param string $id
     * @return bool
     * @throws Exception
     */
    public function del(string $id): bool;
}
<?php

namespace Chowhwei\Store\Contracts;

use Exception;

interface StoreClient
{
    /**
     * @param string $id
     * @param null $default
     * @return mixed
     * @throws Exception
     */
    public function get(string $id, $default = null);

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

    /**
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool;
}
<?php

namespace Chowhwei\Store\Contracts;

interface ClientFactory
{
    /**
     * @param array $config
     * @param string $app
     * @return StoreClient
     */
    public function makeClient($config, $app);
}
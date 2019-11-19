<?php

namespace Chowhwei\Store\Contracts;

interface ClientFactory
{
    /**
     * @param string $type
     * @param array $config
     * @param string $app
     * @return StoreClient
     */
    public function makeClient($type, $config, $app);
}
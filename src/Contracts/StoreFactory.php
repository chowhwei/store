<?php

namespace Chowhwei\Store\Contracts;

interface StoreFactory
{
    /**
     * @param string $app
     * @return ContentStore
     */
    public function app(string $app = 'default');
}
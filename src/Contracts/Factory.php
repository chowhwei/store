<?php

namespace Chowhwei\Store\Contracts;

interface Factory
{
    /**
     * @param string $app
     * @return ContentStore
     */
    public function app(string $app = 'default');
}
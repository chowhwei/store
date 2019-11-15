<?php

namespace Chowhwei\Store\Contracts;

use Exception;

interface ContentStore extends KeyStore
{
    /**
     * @param string $content
     * @return string
     * @throws Exception
     */
    public function storeContent(string $content): string;
}
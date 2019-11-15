<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\ContentStore as ContentStoreContract;
use Exception;

class ContentStore extends KeyStore implements ContentStoreContract
{
    /**
     * @param string $content
     * @return string
     * @throws Exception
     */
    public function storeContent(string $content): string
    {
        $hash = hash('sha256', $content);
        if(is_null($this->ossClient->get($hash))){
            $this->store($hash, $content);
        }
        return $hash;
    }
}
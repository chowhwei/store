<?php

namespace Chowhwei\Store\Store;

use Chowhwei\Store\Contracts\StoreClient;

class ClientFactory implements \Chowhwei\Store\Contracts\ClientFactory
{
    /**
     * @param array $config
     * @param string $app
     * @return StoreClient|FileClient|OssClient
     * @throws \OSS\Core\OssException
     * @throws \Exception
     */
    public function makeClient($config, $app)
    {
        switch (trim(strtolower($config['type']))) {
            case 'oss':
                return new OssClient($config, $app);
                break;
            case 'file':
                return new FileClient($config, $app);
                break;
            default:
                throw new \Exception(strtr('Invalid type :type', [
                    ':type' => $config['type']
                ]));
                break;
        }
    }
}
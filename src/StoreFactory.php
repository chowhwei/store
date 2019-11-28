<?php

namespace Chowhwei\Store;

use Chowhwei\Store\Contracts\ContentStore as ContentStoreContract;
use Chowhwei\Store\Contracts\StoreClient;
use Chowhwei\Store\Contracts\StoreFactory as StoreFactoryContract;
use Chowhwei\Store\Store\ContentStoreMeta;
use Chowhwei\Store\Store\FileClient;
use Chowhwei\Store\Store\NasClient;
use Chowhwei\Store\Store\OssClient;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Model;

class StoreFactory implements StoreFactoryContract
{
    /** @var ContentStore[] $apps */
    protected $apps = [];

    protected $clients = [];
    protected $metas = [];

    /** @var Repository $config */
    protected $config;

    public function __construct()
    {
    }

    /**
     * @param string $app
     * @return ContentStoreContract
     * @throws \OSS\Core\OssException
     */
    public function app(string $app = 'default')
    {
        return $this->getApp($app);
    }

    /**
     * @param string $app
     * @return ContentStoreContract
     * @throws \OSS\Core\OssException
     * @throws \Exception
     */
    protected function getApp($app)
    {
        if (class_exists('Kohana')) {
            $this->config = new Repository(\Kohana::$config->load('store')->as_array());
        } else {
            $this->config = new Repository(config('store'));
        }
        if ($app == 'default') {
            $app = $this->config->get('default');
        }

        $store_config = $this->config->get('store');
        if(!isset($store_config[$app])){
            throw new \Exception(strtr('Invalid store app :app', [
                ':app' => $app
            ]));
        }

        if (!isset($this->apps[$app])) {
            $config = $this->config->get("store.{$app}");

            $ossClient = $this->getClient($config['oss'], $app);
            $nasClient = $this->getClient($config['nas'], $app);
            $meta = isset($config['meta']) && $config['meta'] !== false ? $this->getMeta($config['meta']) : null;

            $cs = new ContentStore($ossClient, $nasClient);
            if (!is_null($meta)) {
                $cs->setMeta($meta);
            }
            $this->apps[$app] = $cs;
        }

        return $this->apps[$app];
    }

    /**
     * @param string $name
     * @return ContentStoreMeta|null
     * @throws \Exception
     */
    protected function getMeta($name)
    {
        if (!isset($this->metas[$name])) {
            $this->metas[$name] = $this->makeMeta($this->config->get("meta.{$name}"));
        }
        return $this->metas[$name];
    }

    /**
     * @param array $config
     * @return ContentStoreMeta
     * @throws \Exception
     */
    protected function makeMeta($config)
    {
        $meta = new ContentStoreMeta();
        $meta->setConnection($config['connection'])
            ->setTable($config['table']);
        return $meta;
    }

    /**
     * @param string $name
     * @param string $dir
     * @return mixed
     * @throws \OSS\Core\OssException
     */
    protected function getClient($name, $dir)
    {
        $index = hash('hash256', $name . $dir);
        if (!isset($this->clients[$index])) {
            $this->clients[$index] = $this->makeClient($this->config->get("client.{$name}"), $dir);
        }

        return $this->clients[$index];
    }

    /**
     * @param array $config
     * @param string $dir
     * @return StoreClient
     * @throws \OSS\Core\OssException
     * @throws \Exception
     */
    protected function makeClient($config, $dir)
    {
        switch (trim(strtolower($config['type']))) {
            case 'oss':
                return new OssClient($config, $dir);
                break;
            case 'nas':
                return new NasClient($config, $dir);
                break;
            default:
                throw new \Exception(strtr('Invalid type :type', [
                    ':type' => $config['type']
                ]));
                break;
        }
    }
}